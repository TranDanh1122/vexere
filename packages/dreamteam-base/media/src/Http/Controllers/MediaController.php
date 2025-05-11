<?php

namespace DreamTeam\Media\Http\Controllers;

use DreamTeam\Base\Http\Controllers\AdminController as BaseController;
use DreamTeam\Media\Facades\RvMedia;
use DreamTeam\Media\Http\Resources\FileResource;
use DreamTeam\Media\Http\Resources\FolderResource;
use DreamTeam\Media\Models\Media;
use DreamTeam\Media\Models\MediaFolder;
use DreamTeam\Media\Repositories\Interfaces\MediaRepositoryInterface;
use DreamTeam\Media\Repositories\Interfaces\MediaFolderInterface;
use DreamTeam\Media\Services\ThumbnailService;
use DreamTeam\Media\Services\UploadsManager;
use DreamTeam\Media\Supports\Zipper;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * @since 24/06/2024 07:50 AM
 */
class MediaController extends BaseController
{
    public function __construct(
        protected MediaRepositoryInterface $fileRepository,
        protected MediaFolderInterface $folderRepository,
        protected UploadsManager $uploadManager
    ) {
    }

    public function getMedia()
    {
        loadStyleAdmin();
        return view('media::index');
    }

    public function getPopup()
    {
        return view('media::popup')->render();
    }

    public function getList(Request $request)
    {
        $files = [];
        $folders = [];
        $breadcrumbs = [];

        $selectedFileId = $request->input('selected_file_id');

        if ($request->has('is_popup') && $selectedFileId) {
            $currentFile = Media::query()->where(
                ['id' => $selectedFileId],
                ['folder_id']
            )->first();

            if ($currentFile) {
                $request->merge(['folder_id' => $currentFile->folder_id]);
            }
        }

        $paramsFolder = [];

        $paramsFile = [
            'order_by' => [
                'is_folder' => 'DESC',
            ],
            'paginate' => [
                'per_page' => $request->integer('posts_per_page', 30),
                'current_paged' => $request->integer('paged', 1),
            ],
            'selected_file_id' => $selectedFileId,
            'is_popup' => $request->input('is_popup'),
            'filter' => $request->input('filter'),
        ];

        $orderBy = $this->transformOrderBy($request->input('sort_by'));

        if (count($orderBy) > 1) {
            $paramsFile['order_by'][$orderBy[0]] = $orderBy[1];
        }

        $search = $request->input('search');

        if ($search) {
            $paramsFolder['condition'] = [
                ['media_folders.name', 'LIKE', '%' . $search . '%'],
            ];

            $paramsFile['condition'] = [
                ['medias.name', 'LIKE', '%' . $search . '%'],
            ];
        }

        $folderId = $request->input('folder_id');

        switch ($request->input('view_in')) {
            case 'all_media':
                $breadcrumbs = [
                    [
                        'id' => 0,
                        'name' => trans('media::media.all_media'),
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M15 8h.01"></path>
                            <path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z"></path>
                            <path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5"></path>
                            <path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3"></path>
                        </svg>',
                    ],
                ];
                if ($request->input('view_type') == 'details') {
                    $folderId = 'all';
                }
                $queried = $this->fileRepository->getFilesByFolderId($folderId, $paramsFile, true, $paramsFolder);
                $folders = FolderResource::collection($queried->where('is_folder', 1));
                $files = FileResource::collection($queried->where('is_folder', 0));

                break;

            case 'trash':
                $breadcrumbs = [
                    [
                        'id' => 0,
                        'name' => trans('media::media.trash'),
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M4 7l16 0"></path>
                            <path d="M10 11l0 6"></path>
                            <path d="M14 11l0 6"></path>
                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
                        </svg>',
                    ],
                ];

                $queried = $this->fileRepository->getTrashed(
                    $folderId,
                    $paramsFile,
                    true,
                    $paramsFolder
                );

                $folders = FolderResource::collection($queried->where('is_folder', 1));

                $files = FileResource::collection($queried->where('is_folder', 0));

                break;

            case 'recent':
                $breadcrumbs = [
                    [
                        'id' => 0,
                        'name' => trans('media::media.recent'),
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                            <path d="M12 7v5l3 3"></path>
                        </svg>',
                    ],
                ];

                if (! count($request->input('recent_items', []))) {
                    break;
                }

                $queried = $this->fileRepository->getFilesByFolderId(
                    0,
                    array_merge($paramsFile, ['recent_items' => $request->input('recent_items', [])]),
                    false,
                    $paramsFolder
                );

                $files = FileResource::collection($queried);

                break;
        }

        $breadcrumbs = array_merge($breadcrumbs, $this->getBreadcrumbs($request));

        return RvMedia::responseSuccess([
            'files' => $files,
            'folders' => $folders,
            'breadcrumbs' => $breadcrumbs,
            'selected_file_id' => $selectedFileId,
        ]);
    }

    protected function transformOrderBy(string|null $orderBy): array
    {
        $result = explode('-', $orderBy);
        if (! count($result) == 2) {
            return ['name', 'asc'];
        }

        return $result;
    }

    protected function getBreadcrumbs(Request $request): array
    {
        $folderId = $request->input('folder_id');

        if (! $folderId) {
            return [];
        }

        if ($request->input('view_in') == 'trash') {
            $folder = MediaFolder::query()->withTrashed()->find($folderId);
        } else {
            $folder = MediaFolder::query()->find($folderId);
        }

        if (empty($folder)) {
            return [];
        }

        $breadcrumbs = [
            [
                'name' => $folder->name,
                'id' => $folder->id,
            ],
        ];

        $child = $this->folderRepository->getBreadcrumbs($folder->parent_id);
        if (! empty($child)) {
            return array_merge($child, $breadcrumbs);
        }

        return $breadcrumbs;
    }

    public function postGlobalActions(Request $request, ThumbnailService $thumbnailService)
    {
        $response = RvMedia::responseError(trans('media::media.invalid_action'));

        $type = $request->input('action');

        switch ($type) {
            case 'trash':
                $error = false;
                foreach ($request->input('selected') as $item) {
                    $id = $item['id'];
                    if (! $item['is_folder']) {
                        try {
                            $this->fileRepository->deleteBy(['id' => $id]);
                        } catch (Exception $exception) {
                            Log::error($exception);
                            $error = true;
                        }
                    } else {
                        $this->folderRepository->deleteFolder($id);
                    }
                }

                if ($error) {
                    $response = RvMedia::responseError(trans('media::media.trash_error'));

                    break;
                }

                $response = RvMedia::responseSuccess([], trans('media::media.trash_success'));

                break;

            case 'restore':
                $error = false;
                foreach ($request->input('selected') as $item) {
                    $id = $item['id'];
                    if (! $item['is_folder']) {
                        try {
                            $this->fileRepository->restoreBy(['id' => $id]);
                        } catch (Exception $exception) {
                            Log::error($exception);
                            $error = true;
                        }
                    } else {
                        $this->folderRepository->restoreFolder($id);
                    }
                }

                if ($error) {
                    $response = RvMedia::responseError(trans('media::media.restore_error'));

                    break;
                }

                $response = RvMedia::responseSuccess([], trans('media::media.restore_success'));

                break;

            case 'make_copy':
                foreach ($request->input('selected', []) as $item) {
                    $id = $item['id'];
                    if (! $item['is_folder']) {
                        /**
                         * @var Media $file
                         */
                        $file = Media::query()->find($id);

                        if (! $file) {
                            break;
                        }

                        $this->copyFile($file);
                    } else {
                        $oldFolder = MediaFolder::query()->find($id);

                        if (! $oldFolder) {
                            break;
                        }

                        $folderData = $oldFolder->replicate()->toArray();

                        $folderData['slug'] = $this->folderRepository->createSlug(
                            $oldFolder->name,
                            $oldFolder->parent_id
                        );
                        $folderData['name'] = $oldFolder->name . '-(copy)';
                        $folderData['user_id'] = Auth::guard('admin')->id();
                        $folder = $this->folderRepository->createFromArray($folderData);

                        $files = $this->fileRepository->getFilesByFolderId($id, [], false);
                        foreach ($files as $file) {
                            $this->copyFile($file, $folder->id);
                        }

                        $children = $this->folderRepository->getAllChildFolders($id);
                        foreach ($children as $parentId => $child) {
                            if ($parentId != $oldFolder->getKey()) {
                                $folder = MediaFolder::query()->find($parentId);

                                if (! $folder) {
                                    break;
                                }

                                $folderData = $folder->replicate()->toArray();

                                $folderData['slug'] = $this->folderRepository->createSlug(
                                    $oldFolder->name,
                                    $oldFolder->parent_id
                                );
                                $folderData['name'] = $oldFolder->name . '-(copy)';
                                $folderData['user_id'] = Auth::guard('admin')->id();
                                $folderData['parent_id'] = $folder->id;
                                $folder = MediaFolder::query()->create($folderData);

                                $parentFiles = $this->fileRepository->getFilesByFolderId($parentId, [], false);
                                foreach ($parentFiles as $parentFile) {
                                    $this->copyFile($parentFile, $folder->id);
                                }
                            }

                            foreach ($child as $sub) {
                                /**
                                 * @var MediaFolder $sub
                                 */
                                $subFiles = $this->fileRepository->getFilesByFolderId($sub->getKey(), [], false);

                                $subFolderData = $sub->replicate()->toArray();

                                $subFolderData['user_id'] = Auth::guard('admin')->id();
                                $subFolderData['parent_id'] = $folder->id;

                                $sub = MediaFolder::query()->create($subFolderData);

                                foreach ($subFiles as $subFile) {
                                    $this->copyFile($subFile, $sub->getKey());
                                }
                            }
                        }

                        $allFiles = Storage::allFiles($this->folderRepository->getFullPath($oldFolder->getKey()));
                        foreach ($allFiles as $file) {
                            Storage::copy($file, str_replace($oldFolder->slug, $folder->slug, $file));
                        }
                    }
                }

                $response = RvMedia::responseSuccess([], trans('media::media.copy_success'));

                break;

            case 'delete':
                foreach ($request->input('selected') as $item) {
                    $id = $item['id'];
                    if (! $item['is_folder']) {
                        try {
                            $this->fileRepository->forceDelete(['id' => $id]);
                        } catch (Exception $exception) {
                            Log::error($exception);
                        }
                    } else {
                        $this->folderRepository->deleteFolder($id, true);
                    }
                }

                $response = RvMedia::responseSuccess([], trans('media::media.delete_success'));

                break;

            case 'crop':
                $validated = Validator::validate($request->input(), [
                    'imageId' => ['required', 'string', 'exists:medias,id'],
                    'cropData' => ['required', 'json'],
                ]);

                $data = json_decode($validated['cropData'], true);

                $cropData = Validator::validate($data, [
                    'x' => ['required', 'numeric'],
                    'y' => ['required', 'numeric'],
                    'width' => ['required', 'numeric'],
                    'height' => ['required', 'numeric'],
                ]);

                /**
                 * @var Media $file
                 */
                $file = Media::query()->findOrFail($validated['imageId']);

                if (! $file->canGenerateThumbnails()) {
                    $response = RvMedia::responseError(trans('media::media.failed_to_crop_image'));

                    break;
                }

                $fileUrl = $file->url;
                $parsedUrl = parse_url($fileUrl);

                if (isset($parsedUrl['query'])) {
                    $fileUrl = str_replace('?' . $parsedUrl['query'], '', $fileUrl);
                }

                $thumbnailService
                    ->setImage(RvMedia::getRealPath($fileUrl))
                    ->setSize((int)$cropData['width'], (int)$cropData['height'])
                    ->setCoordinates((int)$cropData['x'], (int)$cropData['y'])
                    ->setDestinationPath(File::dirname($fileUrl))
                    ->setFileName(File::name($fileUrl) . '.' . File::extension($fileUrl))
                    ->save('crop');

                $file->url = $fileUrl . '?v=' . time();
                $file->save();

                RvMedia::generateThumbnails($file);

                $response = RvMedia::responseSuccess([], trans('media::media.crop_success'));

                break;

            case 'rename':
                Validator::validate($request->input(), [
                    'selected' => ['required', 'array'],
                    'selected.*.id' => ['required', 'string'],
                    'selected.*.name' => ['required', 'string'],
                    'selected.*.is_folder' => ['required', 'boolean'],
                    'selected.*.rename_physical_file' => ['sometimes', 'boolean'],
                ]);

                foreach ($request->input('selected') as $item) {
                    $id = $item['id'];

                    if (! $item['is_folder']) {
                        /**
                         * @var Media $file
                         */
                        $file = Media::query()->find($id);

                        if (! empty($file)) {
                            RvMedia::renameFile(
                                file: $file,
                                newName: $item['name'],
                                renameOnDisk: Arr::get($item, 'rename_physical_file', false)
                            );
                        }
                    } else {
                        $name = $item['name'];
                        /**
                         * @var MediaFolder $folder
                         */
                        $folder = MediaFolder::query()->find($id);

                        if (! empty($folder)) {
                            RvMedia::renameFolder(
                                folder: $folder,
                                newName: $name,
                                renameOnDisk: Arr::get($item, 'rename_physical_file', false)
                            );
                        }
                    }
                }

                $response = RvMedia::responseSuccess([], trans('media::media.rename_success'));

                break;

            case 'alt_text':
                foreach ($request->input('selected') as $item) {
                    if (! $item['id']) {
                        continue;
                    }

                    Media::query()->where('id', $item['id'])->update(['title' => $item['alt']]);
                }

                $response = RvMedia::responseSuccess([], trans('media::media.update_alt_text_success'));

                break;
            case 'empty_trash':
                $this->fileRepository->emptyTrash();
                $this->folderRepository->emptyTrash();

                $response = RvMedia::responseSuccess([], trans('media::media.empty_trash_success'));

                break;

            case 'properties':
                Validator::validate($request->input(), [
                    'color' => ['required', 'string', Rule::in(RvMedia::getFolderColors())],
                    'selected' => ['required', 'array'],
                    'selected.*' => ['required', 'string', 'exists:media_folders,id'],
                ]);

                MediaFolder::query()->whereIn('id', $request->input('selected'))->update([
                    'color' => $request->input('color'),
                ]);

                $response = RvMedia::responseSuccess([], trans('media::media.update_properties_success'));

                break;
        }

        return $response;
    }

    protected function copyFile(Media $file, int|string|null $newFolderId = null)
    {
        $file = $file->replicate();
        $file->user_id = Auth::guard('admin')->id();

        if ($newFolderId == null) {
            $file->name = $file->name . '-(copy)';

            $path = '';

            $folderPath = File::dirname($file->url);
            if ($folderPath) {
                $path = $folderPath . '/' . $path;
            }

            $path = $path . File::name($file->url) . '-(copy)' . '.' . File::extension($file->url);

            $filePath = RvMedia::getRealPath($file->url);
            if (Storage::exists($filePath)) {
                $content = File::get($filePath);

                $this->uploadManager->saveFile($path, $content);
                $file->url = $path;

                RvMedia::generateThumbnails($file);
            }
        } else {
            $file->url = str_replace(
                RvMedia::getRealPath(File::dirname($file->url)),
                RvMedia::getRealPath($this->folderRepository->getFullPath($newFolderId)),
                $file->url
            );

            $file->folder_id = $newFolderId;
        }

        unset($file->is_folder);
        unset($file->slug);
        unset($file->parent_id);
        unset($file->color);
        unset($file->alt);
        $file->save();

        return $file;
    }

    public function download(Request $request)
    {
        $items = $request->input('selected', []);

        if (count($items) == 1 && ! $items[0]['is_folder']) {
            $file = Media::query()->withTrashed()->find($items[0]['id']);
            if (! empty($file) && $file->type != 'video') {
                $filePath = RvMedia::getRealPath($file->url);

                if (! RvMedia::isUsingCloud()) {
                    if (! File::exists($filePath)) {
                        return RvMedia::responseError(trans('media::media.file_not_exists'));
                    }

                    return response()->download($filePath, Str::slug($file->name));
                }

                return response()->make(Http::withoutVerifying()->get($filePath)->body(), 200, [
                    'Content-type' => $file->mime_type,
                    'Content-Disposition' => 'attachment; filename="' . $file->name . '.' . File::extension($file->url) . '"',
                ]);
            }
        } else {
            $fileName = Storage::disk('local')->path('download-' . Carbon::now()->format('Y-m-d-h-i-s') . '.zip');
            $zip = new Zipper();
            $zip->make($fileName);
            foreach ($items as $item) {
                $id = $item['id'];
                if (! $item['is_folder']) {
                    $file = Media::query()->withTrashed()->find($id);
                    if (! empty($file) && $file->type != 'video') {
                        $filePath = RvMedia::getRealPath($file->url);
                        if (! RvMedia::isUsingCloud()) {
                            if (File::exists($filePath)) {
                                $zip->add($filePath);
                            }
                        } else {
                            $zip->addString(
                                File::basename($file),
                                Http::withoutVerifying()->get($filePath)->body()
                            );
                        }
                    }
                } else {
                    $folder = MediaFolder::query()->withTrashed()->find($id);
                    if (! empty($folder)) {
                        if (! RvMedia::isUsingCloud()) {
                            $folderPath = RvMedia::getRealPath($this->folderRepository->getFullPath($folder->id));
                            if (File::isDirectory($folderPath)) {
                                $zip->add($folderPath);
                            }
                        } else {
                            $allFiles = Storage::allFiles($this->folderRepository->getFullPath($folder->id));
                            foreach ($allFiles as $file) {
                                $zip->addString(
                                    File::basename($file),
                                    Http::withoutVerifying()->get(RvMedia::getRealPath($file))->body()
                                );
                            }
                        }
                    }
                }
            }

            $zip = null;

            if (File::exists($fileName)) {
                return response()
                    ->download($fileName, File::name($fileName))
                    ->deleteFileAfterSend();
            }

            return RvMedia::responseError(trans('media::media.download_file_error'));
        }

        return RvMedia::responseError(trans('media::media.can_not_download_file'));
    }

    public function getContent(Request $request)
    {
        $url = $request->url;
        $headers = get_headers($url, 1);
        header("Content-Type: " . $headers["Content-Type"]);

        readfile($url);
    }
}
