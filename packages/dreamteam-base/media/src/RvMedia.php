<?php

namespace DreamTeam\Media;

use Aws\Exception\AwsException;
use Aws\S3\S3Client;
use Exception;
use DreamTeam\Base\Facades\BaseHelper;
use DreamTeam\Base\Facades\Html;
use DreamTeam\Media\Events\MediaFileRenamed;
use DreamTeam\Media\Events\MediaFileRenaming;
use DreamTeam\Media\Events\MediaFileUploaded;
use DreamTeam\Media\Events\MediaFolderRenamed;
use DreamTeam\Media\Events\MediaFolderRenaming;
use DreamTeam\Media\Http\Resources\FileResource;
use DreamTeam\Media\Models\Media;
use DreamTeam\Media\Models\MediaFolder;
use DreamTeam\Media\Services\ThumbnailService;
use DreamTeam\Media\Services\UploadsManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\Encoders\AutoEncoder;
use Intervention\Image\ImageManager;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\UnableToWriteFile;
use Symfony\Component\Mime\MimeTypes;
use Throwable;
use Intervention\Image\Encoders\WebpEncoder;
use DreamTeam\Base\Supports\SystemManagement;
use DreamTeam\Media\Storage\BunnyCDN\BunnyCDNClient;

class RvMedia
{
    protected array $permissions = [];
    public array $thumbnailModule = [];

    public function __construct(protected UploadsManager $uploadManager, protected ThumbnailService $thumbnailService)
    {
        $this->permissions = $this->getConfig('permissions', []);
        $this->thumbnailModule = $this->getConfig('thumbnail_module', []);
    }

    public function getThumbnailModules(): array
    {
        $this->thumbnailModule = apply_filters(FILTER_THUMBNAIL_MODULE_NAME, $this->thumbnailModule);
        return $this->thumbnailModule;
    }

    public function renderHeader(): string
    {
        $urls = $this->getUrls();

        return view('media::header', compact('urls'))->render();
    }

    public function getUrls(): array
    {
        return [
            'base_url' => url(''),
            'base' => route('media.index'),
            'get_media' => route('media.list'),
            'create_folder' => route('media.folders.create'),
            'popup' => route('media.popup'),
            'download' => route('media.download'),
            'upload_file' => route('media.files.upload'),
            'get_breadcrumbs' => route('media.breadcrumbs'),
            'global_actions' => route('media.global_actions'),
            'media_upload_from_editor' => route('media.files.upload.from.editor'),
            'download_url' => route('media.download_url'),
        ];
    }

    public function renderFooter(): string
    {
        return view('media::footer')->render();
    }

    public function renderContent(): string
    {
        $sorts = [
            'name-asc' => [
                'label' => trans('media::media.file_name_asc'),
                'icon' => 'ti ti-sort-ascending-letters',
            ],
            'name-desc' => [
                'label' => trans('media::media.file_name_desc'),
                'icon' => 'ti ti-sort-descending-letters',
            ],
            'created_at-asc' => [
                'label' => trans('media::media.uploaded_date_asc'),
                'icon' => 'ti ti-sort-ascending-numbers',
            ],
            'created_at-desc' => [
                'label' => trans('media::media.uploaded_date_desc'),
                'icon' => 'ti ti-sort-descending-numbers',
            ],
            'size-asc' => [
                'label' => trans('media::media.size_asc'),
                'icon' => 'ti ti-sort-ascending-2',
            ],
            'size-desc' => [
                'label' => trans('media::media.size_desc'),
                'icon' => 'ti ti-sort-descending-2',
            ],
        ];

        return view('media::content', compact('sorts'))->render();
    }

    public function responseSuccess(array $data, string|null $message = null): JsonResponse
    {
        return response()->json([
            'error' => false,
            'data' => $data,
            'message' => $message,
        ]);
    }

    public function responseError(
        string $message,
        array $data = [],
        int|null $code = null,
        int $status = 200
    ): JsonResponse {
        return response()->json([
            'error' => true,
            'message' => $message,
            'data' => $data,
            'code' => $code,
        ], $status);
    }

    public function getAllImageSizes(string $moduleName, string|null $url): array
    {
        if (!isset($this->getSizes()[$moduleName])) return [];
        $images = [];
        foreach ($this->getSizes()[$moduleName] as $sizeKey => $size) {
            $images = $this->getImageUrl($url, $moduleName, $sizeKey);
        }

        return $images;
    }

    public function getSizes(): array
    {
        $sizes = $this->getConfig('sizes', []);
        $data = [];
        foreach ($this->getThumbnailModules() as $moduleName => $serviceName) {
            foreach ($sizes as $name => $size) {
                $size = explode('x', $size);

                $settingName = 'media_' . $moduleName . '_sizes_' . $name;

                $width = getMediaConfig($settingName . '_width', $size[0]);

                $height = getMediaConfig($settingName . '_height', $size[1]);

                if (!$width && !$height) {
                    continue;
                }

                if (!$width) {
                    $width = 'rate';
                }

                if (!$height) {
                    $height = 'rate';
                }

                $data[$moduleName][$name] = $width . 'x' . $height;
            }
        }

        return $data;
    }

    public function getImageUrl(
        string|null $url,
        string $moduleName = null,
        $size = null,
        bool $relativePath = false,
        $default = null
    ): string|null {
        if (empty($url)) {
            return $default ?? $this->getDefaultImage(false, $size);
        }

        $url = trim($url);

        if (empty($url)) {
            return $default;
        }

        if (Str::startsWith($url, ['data:image/png;base64,', 'data:image/jpeg;base64,', 'https://', 'http://'])) {
            return $url;
        }

        if (empty($size) || $url == '__value__') {
            if ($relativePath) {
                return $url;
            }

            return $this->url($url);
        }

        if ($url == $this->getDefaultImage(false, $size)) {
            return url($url);
        }

        if (
            array_key_exists($moduleName, $this->getSizes()) &&
            array_key_exists($size, $this->getSizes()[$moduleName]) &&
            $this->canGenerateThumbnails($this->getMimeType($this->getRealPath($url)))
        ) {
            $url = $this->getThumbnailPath($moduleName, $size, $url);
        }

        if ($relativePath) {
            return $url;
        }

        if ($url == '__image__') {
            return $this->url($default);
        }

        return $this->url($url);
    }

    public function url(string|null $path): string
    {
        if($path) {
            $path = trim($path);
        }

        if (Str::contains($path, ['http://', 'https://']) || Str::startsWith($path, ['data:image/png;base64,', 'data:image/jpeg;base64,', 'data:image'])) {
            return $path;
        }

        if (config('filesystems.default') === 'do_spaces' && (int)setting('media_do_spaces_cdn_enabled')) {
            $customDomain = setting('media_do_spaces_cdn_custom_domain');

            if ($customDomain) {
                return $customDomain . '/' . ltrim($path, '/');
            }

            return str_replace('.digitaloceanspaces.com', '.cdn.digitaloceanspaces.com', Storage::url($path));
        }
        return Storage::url($path);
    }

    public function getDefaultImage(bool $relative = false, string|null $size = null): string|null
    {
        $default = $this->getConfig('default_image');

        if ($placeholder = getMediaConfig('media_default_placeholder_image')) {
            return Storage::url($placeholder);
        }

        if ($relative) {
            return $default;
        }

        return $default ? url($default) : $default;
    }

    public function getSize(string $moduleName, string $name): string|null
    {
        if (!isset($this->getSizes()[$moduleName])) return null;
        return Arr::get($this->getSizes()[$moduleName], $name);
    }

    public function deleteFile(Media $file): bool
    {
        if (empty($file->url)) return true;

        $this->deleteThumbnails($file);

        return Storage::delete([$file->url, $file->url . '.webp']);
    }

    public function deleteThumbnails(Media $file): bool
    {
        if (!$file->canGenerateThumbnails()) {
            return false;
        }
        $files = [];
        foreach ($this->getSizes() as $moduleName => $sizes) {
            foreach ($sizes as $sizeKey => $size) {
                $thumbnailPath = $this->getThumbnailPath($moduleName, $sizeKey, $file->url);
                $files[] = $thumbnailPath;
                $files[] = $thumbnailPath . '.webp';
            }
        }

        return Storage::delete($files);
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }

    public function setPermissions(array $permissions): void
    {
        $this->permissions = $permissions;
    }

    public function removePermission(string $permission): void
    {
        Arr::forget($this->permissions, $permission);
    }

    public function addPermission(string $permission): void
    {
        $this->permissions[] = $permission;
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions);
    }

    public function hasAnyPermission(array $permissions): bool
    {
        $hasPermission = false;
        foreach ($permissions as $permission) {
            if (in_array($permission, $this->permissions)) {
                $hasPermission = true;

                break;
            }
        }

        return $hasPermission;
    }

    public function addSize(string $name, int|string $width, int|string $height = 'auto'): self
    {
        if (!$width) {
            $width = 'auto';
        }

        if (!$height) {
            $height = 'auto';
        }

        config(['dreamteam_media.sizes.' . $name => $width . 'x' . $height]);

        return $this;
    }

    public function removeSize(string $name): self
    {
        $sizes = $this->getSizes();
        Arr::forget($sizes, $name);

        config(['dreamteam_media.sizes' => $sizes]);

        return $this;
    }

    public function uploadFromEditor(
        Request $request,
        int|string|null $folderId = 0,
        $folderName = null,
        string $fileInput = 'upload'
    ) {
        $validator = Validator::make($request->all(), [
            'upload' => $this->imageValidationRule(),
        ]);

        if ($validator->fails()) {
            return response('<script>alert("' . trans('media::media.can_not_detect_file_type') . '")</script>')
                ->header('Content-Type', 'text/html');
        }

        $folderName = $folderName ?: $request->input('upload_type');

        $result = $this->handleUpload($request->file($fileInput), $folderId, $folderName);

        if (!$result['error']) {
            $file = $result['data'];
            if (!$request->input('CKEditorFuncNum')) {
                return response()->json([
                    'fileName' => File::name($this->url($file->url)),
                    'uploaded' => 1,
                    'url' => $this->url($file->url),
                ]);
            }

            return response(
                '<script>window.parent.CKEDITOR.tools.callFunction("' . $request->input('CKEditorFuncNum') .
                    '", "' . $this->url($file->url) . '", "");</script>'
            )
                ->header('Content-Type', 'text/html');
        }

        return response()->json([
            'uploaded' => 0,
            'error' => [
                'message' => Arr::get($result, 'message_text'),
            ],
        ]);
    }

    public function handleUpload(
        ?UploadedFile $fileUpload,
        int|string|null $folderId = 0,
        string|null $folderSlug = null,
        bool $skipValidation = false,
        string|null $allowWebp = null,
        string|null $allowThumb = null,
        string|null $moduleName = null,
    ): array {
        $storageChecker = $this->alertStorageSize();
        if (isset($storageChecker['full_storage']) && $storageChecker['full_storage']) {
            return [
                'error' => true,
                ...$storageChecker,
            ];
        }
        $request = request();
        if ($uploadPath = $request->input('path')) {
            $folderId = $this->handleTargetFolder($folderId, $uploadPath);
        }
        if (!$fileUpload) {
            return [
                'error' => true,
                'message' => trans('media::media.can_not_detect_file_type'),
            ];
        }

        $allowedMimeTypes = $this->getConfig('allowed_mime_types');

        if (!$this->isChunkUploadEnabled()) {
            if (!$skipValidation) {
                $validator = Validator::make(['uploaded_file' => $fileUpload], [
                    'uploaded_file' => 'required|mimes:' . $allowedMimeTypes,
                ]);

                if ($validator->fails()) {
                    return [
                        'error' => true,
                        'message' => $validator->getMessageBag()->first(),
                    ];
                }
            }

            $maxUploadFilesizeAllowed = setting('max_upload_filesize');

            if (
                $maxUploadFilesizeAllowed
                && ($fileUpload->getSize() / 1024) / 1024 > (float)$maxUploadFilesizeAllowed
            ) {
                return [
                    'error' => true,
                    'message' => trans('media::media.file_too_big_readable_size', [
                        'size' => BaseHelper::humanFilesize($maxUploadFilesizeAllowed * 1024 * 1024),
                    ]),
                ];
            }

            $maxSize = $this->getServerConfigMaxUploadFileSize();

            if ($fileUpload->getSize() / 1024 > (int)$maxSize) {
                return [
                    'error' => true,
                    'message' => trans('media::media.file_too_big_readable_size', [
                        'size' => BaseHelper::humanFilesize($maxSize),
                    ]),
                ];
            }
        }

        try {
            $fileExtension = $fileUpload->getClientOriginalExtension() ?: $fileUpload->guessExtension();
            if (!$skipValidation && !in_array(strtolower($fileExtension), explode(',', $allowedMimeTypes))) {
                return [
                    'error' => true,
                    'message' => trans('media::media.can_not_detect_file_type'),
                ];
            }

            if ($folderId == 0 && !empty($folderSlug)) {
                if (str_contains($folderSlug, '/')) {
                    $paths = array_filter(explode('/', $folderSlug));
                    foreach ($paths as $folder) {
                        $folderId = $this->createFolder($folder, $folderId, true);
                    }
                } else {
                    $folderId = $this->createFolder($folderSlug, $folderId, true);
                }
            }

            // nếu k có folder thì chèn vào folder uploads
            $isRefreshFolder = false;
            if (!$folderId) {
                $folderId = MediaFolder::where('slug', 'uploads')->first()->id ?? 0;
                if ($folderId) $isRefreshFolder = true;
            }

            $file = new Media();

            $file->name = Media::createName(
                File::name($fileUpload->getClientOriginalName()),
                $folderId
            );

            $folderPath = MediaFolder::getFullPath($folderId);

            $fileName = Media::createSlug(
                $file->name,
                $fileExtension,
                ($folderPath ?: '')
            );

            $filePath = $fileName;

            if ($folderPath) {
                $filePath = $folderPath . '/' . $filePath;
            }

            if ($this->canGenerateThumbnails($fileUpload->getMimeType())) {
                $content = $fileUpload->get();
                $quality = ((int) getMediaConfig('media_optimize_image_quality', 0));
                if ((bool) getMediaConfig('media_optimize_image_enabled', 0) && $quality > 0 && $quality <= 100) {
                    $content = $this->imageManager()->read($fileUpload->get())->encode(new AutoEncoder(quality: $quality));
                }
            } else {
                $content = $fileUpload->get();
            }
            $this->uploadManager->saveFile($filePath, $content, $fileUpload);
            $data = $this->uploadManager->fileDetails($filePath);

            $file->url = $data['url'];
            $file->title = $file->name;
            $file->size = $data['size'];
            $file->mime_type = $data['mime_type'];
            $file->folder_id = $folderId;
            $file->user_id = Auth::guard('admin')->check() ? Auth::guard('admin')->id() : 0;
            $file->options = $request->input('options', []);
            $file->save();

            MediaFileUploaded::dispatch($file);
            $folderIds = json_decode(setting('media_folders_can_add_watermark', ''), true);

            if (
                empty($folderIds) ||
                in_array($file->folder_id, $folderIds) ||
                !empty(array_intersect($file->folder->parents->pluck('id')->all(), $folderIds))
            ) {
                $this->insertWatermark($file->url);
            }
            $this->makeWebpImage($file->url);
            if ($moduleName && $allowThumb && $allowThumb == 'yes') {
                $this->generateThumbnails($file, $fileUpload, $moduleName);
                if ($allowWebp && $allowWebp == 'yes') {
                    $this->makeWebpForThumbnailImage($file, $allowWebp, $moduleName);
                }
            }

            return [
                'error' => false,
                'refresh_folder' => $isRefreshFolder,
                'data' => new FileResource($file),
            ];
        } catch (UnableToWriteFile $exception) {
            $message = $exception->getMessage();

            if (!$this->isUsingCloud()) {
                $message = trans('media::media.unable_to_write', ['folder' => $this->getUploadPath()]);
            }

            return [
                'error' => true,
                'message' => $message,
            ];
        } catch (Throwable $exception) {
            return [
                'error' => true,
                'message' => $exception->getMessage(),
            ];
        }
    }

    /**
     * Returns a file size limit in bytes based on the PHP upload_max_filesize and post_max_size
     */
    public function getServerConfigMaxUploadFileSize(): float
    {
        // Start with post_max_size.
        $maxSize = $this->parseSize(@ini_get('post_max_size'));

        // If upload_max_size is less, then reduce. Except if upload_max_size is
        // zero, which indicates no limit.
        $uploadMax = $this->parseSize(@ini_get('upload_max_filesize'));
        if ($uploadMax > 0 && $uploadMax < $maxSize) {
            $maxSize = $uploadMax;
        }

        return $maxSize;
    }

    public function parseSize(int|string $size): float
    {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
        $size = (int)preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
        if ($unit) {
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        }

        return round($size);
    }

    public function generateThumbnails(Media $file, UploadedFile $fileUpload = null, string|null $moduleName = null): bool
    {
        if (!$file->canGenerateThumbnails()) {
            return false;
        }

        if (!isset($this->getSizes()[$moduleName])) {
            return false;
        }

        if (!$this->isUsingCloud() && !File::exists($this->getRealPath($file->url))) {
            return false;
        }

        $fileUpload = $this->isUsingCloud() ? Storage::get($file->url) : $this->getRealPath($file->url);

        foreach ($this->getSizes()[$moduleName] as $resizeKey => $size) {
            $readableSize = explode('x', $size);
            $destinationPath = $this->getThumbnailDestinationPath($moduleName, $resizeKey) . '/' . File::dirname($file->url);
            $this->thumbnailService
                ->setImage($fileUpload)
                ->setSize($readableSize[0], $readableSize[1])
                ->setDestinationPath($destinationPath)
                ->setFileName(File::name($file->url) . '.' . File::extension($file->url))
                ->save('resize');
        }
        return true;
    }

    public function insertWatermark(string $image): bool
    {
        if (!$image || !setting('media_watermark_enabled', $this->getConfig('watermark.enabled'))) {
            return false;
        }

        $watermarkImage = setting('media_watermark_source', $this->getConfig('watermark.source'));

        if (!$watermarkImage) {
            return false;
        }

        $watermarkPath = $this->getRealPath($watermarkImage);

        if (!File::exists($watermarkPath) && !Storage::exists($watermarkImage)) {
            return false;
        }

        $watermark = $this->imageManager()->read($this->isUsingCloud() ? Storage::get($watermarkImage) : $watermarkPath);

        $imageSource = $this->imageManager()->read($this->isUsingCloud() ? Storage::get($image) : $this->getRealPath($image));

        // 10% less than an actual image (play with this value)
        // Watermark will be 10 less than the actual width of the image
        $watermarkSizeConfig = (int)setting(
            'media_watermark_size',
            $this->getConfig('watermark.size')
        );

        // Resize watermark width keep height auto
        if($watermarkSizeConfig && $watermarkSizeConfig > 0 && $watermarkSizeConfig < 100) {
            $watermarkSize = (int)round(
                $imageSource->width() * ($watermarkSizeConfig / 100),
                2
            );
            $watermark->resize($watermarkSize);
        }

        $imageSource->place(
            $watermark,
            setting('media_watermark_position', $this->getConfig('watermark.position')),
            (int)setting('watermark_position_x', $this->getConfig('watermark.x')),
            (int)setting('watermark_position_y', $this->getConfig('watermark.y')),
            (int)setting('media_watermark_opacity', $this->getConfig('watermark.opacity'))
        );

        $destinationPath = sprintf(
            '%s/%s',
            trim(File::dirname($image), '/'),
            File::name($image) . '.' . File::extension($image)
        );

        $this->uploadManager->saveFile($destinationPath, $imageSource->encode(new AutoEncoder()));

        return true;
    }

    public function getThumbnailPath(string $moduleName, string $size, string $fileName): string
    {
        return 'thumbnails/' . $moduleName . '/' . $size . '/' . ltrim($fileName, '/');
    }

    public function getThumbnailDestinationPath(string $moduleName, string $size): string
    {
        return 'thumbnails/' . $moduleName . '/' . $size;
    }

    public function getRealPath(string|null $url): string
    {
        $path = $this->isUsingCloud()
            ? Storage::url($url)
            : Storage::path($url);

        return Arr::first(explode('?v=', $path));
    }

    public function isImage(string $mimeType): bool
    {
        return Str::startsWith($mimeType, 'image/');
    }

    public function isUsingCloud(): bool
    {
        $defaultDisk = config('filesystems.default');

        return config('filesystems.disks.' . $defaultDisk . '.driver', 'local') !== 'local';
    }

    public function uploadFromUrl(
        string $url,
        int|string $folderId = 0,
        string|null $folderSlug = null,
        string|null $defaultMimetype = null,
        string|null $allowWebp = null,
        string|null $allowThumb = null,
        string|null $moduleName = null,
    ): array|null {
        if (empty($url)) {
            return [
                'error' => true,
                'message' => trans('media::media.url_invalid'),
            ];
        }

        $parseUrl = parse_url($url);
        $info = pathinfo($parseUrl['path'] ?? '');

        try {
            $response = Http::withoutVerifying()->get($url);

            if ($response->failed() || !$response->body()) {
                return [
                    'error' => true,
                    'message' => trans('media::media.unable_download_image_from', ['url' => $url]),
                ];
            }

            $contents = $response->body();
        } catch (Throwable $exception) {
            return [
                'error' => true,
                'message' => $exception->getMessage(),
            ];
        }

        $path = '/tmp';
        File::ensureDirectoryExists($path);

        $path = $path . '/' . Str::limit($info['basename'], 500, '');
        file_put_contents($path, $contents);

        $fileUpload = $this->newUploadedFile($path, $defaultMimetype);

        $result = $this->handleUpload($fileUpload, $folderId, $folderSlug, false, $allowWebp, $allowThumb, $moduleName);

        File::delete($path);

        return $result;
    }

    public function uploadFromPath(
        string $path,
        int|string $folderId = 0,
        string|null $folderSlug = null,
        string|null $defaultMimetype = null
    ): array {
        if (empty($path)) {
            return [
                'error' => true,
                'message' => trans('media::media.path_invalid'),
            ];
        }

        $fileUpload = $this->newUploadedFile($path, $defaultMimetype);

        return $this->handleUpload($fileUpload, $folderId, $folderSlug);
    }

    public function makeWebpImage(string $path, bool $reMake = false): string
    {
        if ((bool)getMediaConfig('media_show_webp', 1)) {
            $realPath = $this->getRelativePathFromUrl($path);
            if (str_ends_with($path, '.gif') !== false || str_ends_with($path, '.webp')) {
                return $path;
            }

            if (!Storage::exists($realPath)) {
                return $path;
            }

            $webpPath = $realPath . '.webp';
            if (Storage::exists($webpPath) && !$reMake) {
                return $this->url($webpPath);
            }
            try {
                if ($this->isUsingCloud()) {
                    $fileUpload = null;
                    $content = $this->imageManager()->read(Storage::get($realPath));
                } else {
                    $fileUpload = $this->newUploadedFile($this->getRealPath($realPath));
                    $content = $this->imageManager()->read($fileUpload->get());
                }
                $content = $content->encode(new WebpEncoder(quality: 85));
                $this->uploadManager->saveFile($webpPath, $content, $fileUpload);
                return $this->url($webpPath);
            } catch (Exception $e) {
                Log::error('addWebp error for image ' . $path);
                Log::error($e->getMessage());
            }
        }
        return $path;
    }

    public function makeWebpImageForFallBack(string $path): string
    {
        $path = $this->makeWebpImage($path);
        $realPath = $this->getRelativePathFromUrl($path);
        if (str_ends_with($path, '.gif') !== false || str_ends_with($path, '.webp')) {
            return $path;
        }

        if (!Storage::exists($realPath) && Str::startsWith($realPath, ['/thumbnails', 'thumbnails'])) {
            $parseSize = explode('/', ltrim($realPath, '/'));
            $moduleName = $parseSize[1] ?? '';
            $sizeKey = $parseSize[2] ?? '';
            $originPath = ltrim(str_replace('thumbnails/' . $moduleName . '/' . $sizeKey, '' , $realPath), '/');
            $path = $this->addThumnailWithSize($originPath, $realPath, $moduleName, $sizeKey);
            return $this->makeWebpImage($path);
        }
        return $path;

    }

    public function addThumnailWithSize(string $path, string $pathThumbnail, string $moduleName, string $sizeKey): string
    {
        if (str_ends_with($path, '.gif') !== false) {
            return $path;
        }

        try {
            $realPath = $this->getRelativePathFromUrl($path);
            if (!Storage::exists($realPath)) {
                return $this->url($path);
            }
            $realThumbnailpath = $this->getRelativePathFromUrl($pathThumbnail);
            if (Storage::exists($realThumbnailpath)) {
                return $this->url($pathThumbnail);
            }
            $size = $this->getSize($moduleName, $sizeKey);
            if (!$size) return $path;
            $readableSize = explode('x', $size);
            $fileUpload = $this->isUsingCloud() ? Storage::get($realPath) : RvMedia::getRealPath($realPath);
            $destinationPath = $this->getThumbnailDestinationPath($moduleName, $sizeKey) . '/' . File::dirname($realPath);
            $this->thumbnailService
                ->setImage($fileUpload)
                ->setSize($readableSize[0], $readableSize[1])
                ->setDestinationPath($destinationPath)
                ->setFileName(File::name($realThumbnailpath) . '.' . File::extension($realThumbnailpath))
                ->save();
            return $this->url($realThumbnailpath);
        } catch (\Exception $e) {
            Log::error('Add resize error for image ' . $path);
            Log::error($e->getMessage());
            return $path;
        }
    }

    public function reGenerateThumbnails(string $filePath, string $moduleName): bool
    {
        $filePath = $this->getRelativePathFromUrl($filePath);

        if (!isset($this->getSizes()[$moduleName])) {
            return false;
        }
        if (!$this->canGenerateThumbnails($this->getMimeType($filePath))) {
            return false;
        }

        if (!$this->isUsingCloud() && !File::exists($this->getRealPath($filePath))) {
            return false;
        }

        $fileUpload = $this->isUsingCloud() ? Storage::get($filePath) : $this->getRealPath($filePath);
        foreach ($this->getSizes()[$moduleName] as $resizeKey => $size) {
            $readableSize = explode('x', $size);
            $thumbnailName = File::name($filePath) . '.' . File::extension($filePath);
            $destinationPath = $this->getThumbnailDestinationPath($moduleName, $resizeKey) . '/' . File::dirname($filePath);
            $this->thumbnailService
                ->setImage($fileUpload)
                ->setSize($readableSize[0], $readableSize[1])
                ->setDestinationPath($destinationPath)
                ->setFileName($thumbnailName)
                ->save('resize');
            $thumbnailPath = $destinationPath . '/' . $thumbnailName;
            // if (Storage::exists($thumbnailPath . '.webp')) {
                $this->makeWebpImage($thumbnailPath, true);
            // }
        }

        return true;
    }

    public function makeWebpForThumbnailImage(Media $file, string|null $allowWebp = null, string|null $moduleName = null): bool
    {
        if (((bool)getMediaConfig('media_show_webp', 1)) && $allowWebp && $allowWebp == 'yes' && $moduleName && isset($this->getSizes()[$moduleName])) {
            foreach ($this->getSizes()[$moduleName] as $resizeKey => $size) {
                $thumbnailPath = $this->getThumbnailPath($moduleName, $resizeKey, $file->url);
                $this->makeWebpImage($thumbnailPath, true);
            }
        }
        return true;
    }

    public function getStorageDomain(): string
    {
        $mediaDriver = getMediaConfig('media_driver', 'local');
        return config('filesystems.disks.' . $mediaDriver . '.url');
    }

    public function getRelativePathFromUrl(string|null $url): string|null
    {
        if (empty($url)) return $url;
        $url = str_replace($this->getStorageDomain(), '', $url);
        if (!Str::contains($url, ['http://', 'https://'])) {
            return $url;
        }
        $parsedUrl = parse_url($url);
        if (isset($parsedUrl['path'])) {
            return ltrim($parsedUrl['path'], '/');
        }

        return null;
    }

    public function uploadFromBlob(
        UploadedFile $path,
        string|null $fileName = null,
        int|string $folderId = 0,
        string|null $folderSlug = null,
    ): array {
        $fileUpload = new UploadedFile($path, $fileName ?: Str::uuid());

        return $this->handleUpload($fileUpload, $folderId, $folderSlug, true);
    }

    protected function newUploadedFile(string $path, string $defaultMimeType = null): UploadedFile
    {
        $mimeType = $this->getMimeType($path);

        if (empty($mimeType)) {
            $mimeType = $defaultMimeType;
        }

        $fileName = File::name($path);
        $fileExtension = File::extension($path);

        if (empty($fileExtension) && $mimeType) {
            $mimeTypeDetection = (new MimeTypes())->getExtensions($mimeType);

            $fileExtension = Arr::first($mimeTypeDetection);
        }

        return new UploadedFile($path, $fileName . '.' . $fileExtension, $mimeType, null, true);
    }

    public function getUploadPath(): string
    {
        if ($customFolder = $this->getConfig('default_upload_folder')) {
            return public_path($customFolder);
        }

        return is_link(public_path('storage')) ? storage_path('app/public') : public_path('storage');
    }

    public function getUploadURL(): string
    {
        return str_replace('/index.php', '', $this->getConfig('default_upload_url'));
    }

    public function setUploadPathAndURLToPublic(): static
    {
        add_action('init', function () {
            config([
                'filesystems.disks.public.root' => $this->getUploadPath(),
                'filesystems.disks.public.url' => $this->getUploadURL(),
            ]);
        }, 124);

        return $this;
    }

    public function getMimeType(string $url): string|null
    {
        if (!$url) {
            return null;
        }

        try {
            $realPath = $this->getRealPath($url);

            $fileExtension = File::extension($realPath);

            if (!$fileExtension) {
                return null;
            }

            if ($fileExtension == 'jfif') {
                return 'image/jpeg';
            }

            $mimeTypeDetection = new MimeTypes();

            return Arr::first($mimeTypeDetection->getMimeTypes($fileExtension));
        } catch (UnableToRetrieveMetadata) {
            return null;
        }
    }

    public function canGenerateThumbnails(string|null $mimeType): bool
    {
        if (!(bool)getMediaConfig('media_compressed_size', $this->getConfig('generate_thumbnails_enabled'))) {
            return false;
        }

        if (!$mimeType) {
            return false;
        }

        return $this->isImage($mimeType) && !in_array($mimeType, ['image/svg+xml', 'image/x-icon']);
    }

    public function createFolder(string $folderSlug, int|string|null $parentId = 0, bool $force = false): int|string
    {
        $folder = MediaFolder::query()
            ->where([
                'slug' => $folderSlug,
                'parent_id' => $parentId,
            ])
            ->first();

        if (!$folder) {
            if ($force) {
                MediaFolder::query()
                    ->where([
                        'slug' => $folderSlug,
                        'parent_id' => $parentId,
                    ])
                    ->each(fn (MediaFolder $folder) => $folder->forceDelete());
            }

            $folder = MediaFolder::query()->create([
                'user_id' => Auth::guard('admin')->check() ? Auth::guard('admin')->id() : 0,
                'name' => MediaFolder::createName($folderSlug, $parentId),
                'slug' => MediaFolder::createSlug($folderSlug, $parentId),
                'parent_id' => $parentId,
            ]);
        }

        return $folder->id;
    }

    public function handleTargetFolder(int|string|null $folderId = 0, string $filePath = ''): string
    {
        if (str_contains($filePath, '/')) {
            $paths = array_filter(explode('/', $filePath));
            array_pop($paths);
            foreach ($paths as $folder) {
                $folderId = $this->createFolder($folder, $folderId, true);
            }
        }

        return $folderId;
    }

    public function isChunkUploadEnabled(): bool
    {
        return (int)$this->getConfig('chunk.enabled') == 1;
    }

    public function getConfig(string|null $key = null, string|null|array $default = null)
    {
        $configs = config('dreamteam_media');

        if (!$key) {
            return $configs;
        }

        return Arr::get($configs, $key, $default);
    }

    public function imageValidationRule(): string
    {
        return 'required|image|mimes:jpg,jpeg,png,webp,gif,bmp';
    }

    public function turnOffAutomaticUrlTranslationIntoLatin(): bool
    {
        return (int)setting('media_turn_off_automatic_url_translation_into_latin', 0) == 1;
    }

    public function getImageProcessingLibrary(): string
    {
        return setting('media_image_processing_library') ?: 'imagick';
    }

    public function getMediaDriver(): string
    {
        return setting('media_driver', 'local');
    }

    public function setS3Disk(array $config): void
    {
        if (
            !$config['key'] ||
            !$config['secret'] ||
            !$config['region'] ||
            !$config['bucket'] ||
            !$config['url']
        ) {
            return;
        }

        config()->set([
            'filesystems.disks.s3' => [
                'driver' => 's3',
                'visibility' => 'public',
                'throw' => true,
                'key' => $config['key'],
                'secret' => $config['secret'],
                'region' => $config['region'],
                'bucket' => $config['bucket'],
                'url' => $config['url'],
                'endpoint' => $config['endpoint'],
                'use_path_style_endpoint' => $config['use_path_style_endpoint'],
            ],
        ]);
    }

    public function setR2Disk(array $config): void
    {
        if (
            !$config['key'] ||
            !$config['secret'] ||
            !$config['bucket'] ||
            !$config['endpoint']
        ) {
            return;
        }

        config()->set([
            'filesystems.disks.r2' => [
                'driver' => 's3',
                'visibility' => 'public',
                'throw' => true,
                'key' => $config['key'],
                'secret' => $config['secret'],
                'region' => 'auto',
                'bucket' => $config['bucket'],
                'url' => $config['url'],
                'endpoint' => $config['endpoint'],
                'use_path_style_endpoint' => true,
            ],
        ]);
    }

    public function setDoSpacesDisk(array $config): void
    {
        if (
            !$config['key'] ||
            !$config['secret'] ||
            !$config['region'] ||
            !$config['bucket'] ||
            !$config['endpoint']
        ) {
            return;
        }

        config()->set([
            'filesystems.disks.do_spaces' => [
                'driver' => 's3',
                'visibility' => 'public',
                'throw' => true,
                'key' => $config['key'],
                'secret' => $config['secret'],
                'region' => $config['region'],
                'bucket' => $config['bucket'],
                'endpoint' => $config['endpoint'],
            ],
        ]);
    }

    public function setWasabiDisk(array $config): void
    {
        if (
            !$config['key'] ||
            !$config['secret'] ||
            !$config['region'] ||
            !$config['bucket']
        ) {
            return;
        }

        config()->set([
            'filesystems.disks.wasabi' => [
                'driver' => 'wasabi',
                'visibility' => 'public',
                'throw' => true,
                'key' => $config['key'],
                'secret' => $config['secret'],
                'region' => $config['region'],
                'bucket' => $config['bucket'],
                'root' => $config['root'] ?: '/',
            ],
        ]);
    }

    public function setBunnyCdnDisk(array $config): void
    {
        if (
            !$config['hostname'] ||
            !$config['storage_zone'] ||
            !$config['api_key']
        ) {
            return;
        }

        config()->set([
            'filesystems.disks.bunnycdn' => [
                'driver' => 'bunnycdn',
                'visibility' => 'public',
                'throw' => true,
                'hostname' => $config['hostname'],
                'storage_zone' => $config['storage_zone'],
                'api_key' => $config['api_key'],
                'region' => $config['region'],
            ],
        ]);
    }

    public function image(
        string|null $url,
        string $moduleName = null,
        string $alt = null,
        string $size = null,
        bool $useDefaultImage = true,
        array $attributes = [],
        bool $secure = null
    ): HtmlString {
        if (!isset($attributes['loading'])) {
            $attributes['loading'] = 'lazy';
        }

        $defaultImageUrl = $this->getDefaultImage(false, $size);

        if (!$url) {
            $url = $defaultImageUrl;
        }

        $url = $this->getImageUrl($url, $moduleName, $size, false, $useDefaultImage ? $defaultImageUrl : null);

        if (Str::startsWith($url, ['data:image/png;base64,', 'data:image/jpeg;base64,'])) {
            return Html::tag('img', '', [...$attributes, 'src' => $url, 'alt' => $alt]);
        }

        return Html::image($url, $alt, $attributes, $secure);
    }

    public function getFileSize(string|null $path): string|null
    {
        if (!$path || !Storage::exists($path)) {
            return null;
        }

        $size = Storage::size($path);

        if ($size == 0) {
            return '0kB';
        }

        return BaseHelper::humanFilesize($size);
    }

    public function renameFile(Media $file, string $newName, bool $renameOnDisk = true): void
    {
        MediaFileRenaming::dispatch($file, $newName, $renameOnDisk);

        $file->name = Media::createName($newName, $file->folder_id);

        if ($renameOnDisk) {
            $filePath = $this->getRealPath($file->url);

            if (File::exists($filePath)) {
                $newFilePath = str_replace(
                    File::name($file->url),
                    File::name($file->name),
                    $file->url
                );

                File::move($filePath, $this->getRealPath($newFilePath));

                $this->deleteFile($file);

                $file->url = str_replace(
                    File::name($file->url),
                    File::name($file->name),
                    $file->url
                );

                $this->generateThumbnails($file);
            }
        }

        $file->save();

        MediaFileRenamed::dispatch($file);
    }

    public function renameFolder(MediaFolder $folder, string $newName, bool $renameOnDisk = true): void
    {
        MediaFolderRenaming::dispatch($folder, $newName, $renameOnDisk);

        $folder->name = MediaFolder::createName($newName, $folder->parent_id);

        if ($renameOnDisk) {
            $folderPath = MediaFolder::getFullPath($folder->id);

            if (Storage::exists($folderPath)) {
                $newFolderName = MediaFolder::createSlug($newName, $folder->parent_id);

                $newFolderPath = str_replace(
                    File::name($folderPath),
                    $newFolderName,
                    $folderPath
                );

                Storage::move($folderPath, $newFolderPath);

                $folder->slug = $newFolderName;

                $folderPath = "$folderPath/";

                Media::query()
                    ->where('url', 'LIKE', "$folderPath%")
                    ->update([
                        'url' => DB::raw(
                            sprintf(
                                'CONCAT(%s, SUBSTRING(url, LOCATE(%s, url) + LENGTH(%s)))',
                                DB::escape("$newFolderPath/"),
                                DB::escape($folderPath),
                                DB::escape($folderPath)
                            )
                        ),
                    ]);
            }
        }

        $folder->save();

        MediaFolderRenamed::dispatch($folder);
    }

    public function refreshCache(): void
    {
        setting()->forceSet('media_random_hash', md5((string)time()))->save();
    }

    public function getFolderColors(): array
    {
        return $this->getConfig('folder_colors', []);
    }

    public function imageManager(string $driver = null): ImageManager
    {
        if (!$driver) {
            $driver = GdDriver::class;

            if ($this->getImageProcessingLibrary() === 'imagick' && extension_loaded('imagick')) {
                $driver = ImagickDriver::class;
            }
        }
        return new ImageManager($driver);
    }

    public function getSizeMediaBucket()
    {
        $size = 0;
        if (($driver = $this->getMediaDriver()) != 'local') {
            $config = config('filesystems.disks.' . $driver);
            if ($driver != 'bunnycdn') {
                if ($driver == 'wasabi') {
                    $config['endpoint'] = 'https://' . $config['bucket'] . '.s3.' . $config['region'] . '.wasabisys.com/';
                }
                $s3Client = new S3Client([
                    'region'  => $config['region'],
                    'endpoint'  => $config['endpoint'],
                    'version' => 'latest',
                    'credentials' => [
                        'key'    => $config['key'],
                        'secret' => $config['secret'],
                    ],
                ]);

                $bucket = $config['bucket'];

                try {
                    $result = $s3Client->listObjectsV2(['Bucket' => $bucket]);
                    foreach ($result['Contents'] as $object) {
                        $size += $object['Size'];
                    }
                } catch (AwsException $e) {
                    Log::error("Get Size Media Bucket Error: " . $e->getMessage());
                    Log::error($e);
                    $size = -1;
                }
            } else {
                $bunnyCDNClient = new BunnyCDNClient(
                    $config['storage_zone'],
                    $config['api_key'],
                    $config['region']
                );
                $size = $bunnyCDNClient->calculateStorageSize('/');
            }
        } else {
            $size = SystemManagement::getAppSize();
        }
        return $size;
    }

    public function alertStorageSize()
    {
        // disable check
        return ['full_storage' => false];
        $size = Cache::rememberForever('storage_size', function() {
            return $this->getSizeMediaBucket();
        });
        $storageSize = SystemManagement::calculateStorageSize($size);

        return [
            'full_storage' => $storageSize['fullStorage'],
            'addition_storage' => $storageSize['additionInformation'],
            'size_format' => BaseHelper::humanFilesize($size),
            'message' => $storageSize['fullStorage'] ? trans('media::media.alert_storage', ['storage' => BaseHelper::humanFilesize($size), 'maxStorage' => BaseHelper::humanFilesize($storageSize['storage'])]) : '',
            'message_text' => $storageSize['fullStorage'] ? trans('media::media.alert_storage_text', ['storage' => BaseHelper::humanFilesize($size), 'maxStorage' => BaseHelper::humanFilesize($storageSize['storage'])]) : '',
            'warning_storage' => $storageSize['warningStore'],
            'warning_message' => trans('media::media.warning_storage', ['storage' => BaseHelper::humanFilesize($size), 'maxStorage' => BaseHelper::humanFilesize($storageSize['storage'])]),
            'warning_15_day_addition' => $storageSize['warningStoreAddition'] ?? false,
            'warning_15_day_addition_message' => ($storageSize['warningStoreAddition'] ?? false) ? trans('media::media.alert_storage_berfore_date', ['storage' => BaseHelper::humanFilesize($size), 'maxStorage' => BaseHelper::humanFilesize($storageSize['storage']), 'date' => ($storageSize['additionInformation']['end_time'] ?? ''), 'data' => ($storageSize['additionInformation']['storage_size'] ?? '')]) : '',
            'warning_end_addition' => $storageSize['statusTimeStoreAddition'] ?? false,
            'warning_end_addition_message' => ($storageSize['statusTimeStoreAddition'] ?? false) ? trans('media::media.alert_storage_end_date', ['storage' => BaseHelper::humanFilesize($size), 'maxStorage' => BaseHelper::humanFilesize($storageSize['storage']), 'date' => ($storageSize['additionInformation']['end_time'] ?? ''), 'data' => ($storageSize['additionInformation']['storage_size'] ?? '')]) : '',
        ];
    }
}
