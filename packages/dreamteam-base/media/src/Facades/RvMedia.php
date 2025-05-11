<?php

namespace DreamTeam\Media\Facades;

use DreamTeam\Media\RvMedia as BaseRvMedia;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array getThumbnailModules()
 * @method static string renderHeader()
 * @method static array getUrls()
 * @method static string renderFooter()
 * @method static string renderContent()
 * @method static \Illuminate\Http\JsonResponse responseSuccess(array $data, string|null $message = null)
 * @method static \Illuminate\Http\JsonResponse responseError(string $message, array $data = [], int|null $code = null, int $status = 200)
 * @method static array getAllImageSizes(string|null $url)
 * @method static array getSizes()
 * @method static string|null getImageUrl(string|null $url, string $moduleName = null, $size = null, bool $relativePath = false, $default = null)
 * @method static string url(string|null $path)
 * @method static string getDefaultImage(bool $relative = false, string|null $size = null)
 * @method static string|null getSize(string $name)
 * @method static bool deleteFile(\DreamTeam\Media\Models\MediaFile $file)
 * @method static bool deleteThumbnails(\DreamTeam\Media\Models\MediaFile $file)
 * @method static array getPermissions()
 * @method static void setPermissions(array $permissions)
 * @method static void removePermission(string $permission)
 * @method static void addPermission(string $permission)
 * @method static bool hasPermission(string $permission)
 * @method static bool hasAnyPermission(array $permissions)
 * @method static \DreamTeam\Media\RvMedia addSize(string $name, string|int $width, string|int $height = 'auto')
 * @method static \DreamTeam\Media\RvMedia removeSize(string $name)
 * @method static mixed uploadFromEditor(\Illuminate\Http\Request $request, string|int|null $folderId = 0, $folderName = null, string $fileInput = 'upload')
 * @method static array handleUpload(\Illuminate\Http\UploadedFile|null $fileUpload, string|int|null $folderId = 0, string|null $folderSlug = null, bool $skipValidation = false, string|null $allowWebp = null, string|null $allowThumb = null, string|null $moduleName = null)
 * @method static float getServerConfigMaxUploadFileSize()
 * @method static float parseSize(string|int $size)
 * @method static bool generateThumbnails(\DreamTeam\Media\Models\MediaFile $file, \Illuminate\Http\UploadedFile|null $fileUpload = null)
 * @method static bool insertWatermark(string $image)
 * @method static string getRealPath(string|null $url)
 * @method static bool isImage(string $mimeType)
 * @method static bool isUsingCloud()
 * @method static array|null uploadFromUrl(string $url, string|int $folderId = 0, string|null $folderSlug = null, string|null $defaultMimetype = null, string|null $allowWebp = null, string|null $allowThumb = null, string|null $moduleName = null)
 * @method static array uploadFromPath(string $path, string|int $folderId = 0, string|null $folderSlug = null, string|null $defaultMimetype = null)
 * @method static string makeWebpImage(string $path, bool $reMake = false)
 * @method static string makeWebpImageForFallBack(string $path)
 * @method static string addThumnailWithSize(string $path, string $pathThumbnail, string $sizeKey)
 * @method static bool reGenerateThumbnails(string $filePath, string $moduleName)
 * @method static array uploadFromBlob(\Illuminate\Http\UploadedFile $path, string|null $fileName = null, string|int $folderId = 0, string|null $folderSlug = null)
 * @method static string getUploadPath()
 * @method static string getUploadURL()
 * @method static static setUploadPathAndURLToPublic()
 * @method static string|null getMimeType(string $url)
 * @method static bool canGenerateThumbnails(string|null $mimeType)
 * @method static string|int createFolder(string $folderSlug, string|int|null $parentId = 0, bool $force = false)
 * @method static string handleTargetFolder(string|int|null $folderId = 0, string $filePath = '')
 * @method static bool isChunkUploadEnabled()
 * @method static mixed getConfig(string|null $key = null, array|string|null $default = null)
 * @method static string imageValidationRule()
 * @method static bool turnOffAutomaticUrlTranslationIntoLatin()
 * @method static string getImageProcessingLibrary()
 * @method static string getMediaDriver()
 * @method static void setS3Disk(array $config)
 * @method static void setR2Disk(array $config)
 * @method static void setDoSpacesDisk(array $config)
 * @method static void setWasabiDisk(array $config)
 * @method static void setBunnyCdnDisk(array $config)
 * @method static \Illuminate\Support\HtmlString image(string|null $url, string|null $alt = null, string|null $size = null, bool $useDefaultImage = true, array $attributes = [], bool|null $secure = null)
 * @method static string|null getFileSize(string|null $path)
 * @method static void renameFile(\DreamTeam\Media\Models\MediaFile $file, string $newName, bool $renameOnDisk = true)
 * @method static void renameFolder(\DreamTeam\Media\Models\MediaFolder $folder, string $newName, bool $renameOnDisk = true)
 * @method static void refreshCache()
 * @method static array getFolderColors()
 * @method static \Intervention\Image\ImageManager imageManager(string|null $driver = null)
 *
 * @see \DreamTeam\Media\RvMedia
 */
class RvMedia extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return BaseRvMedia::class;
    }
}
