<?php 

// force fore convert to new

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use DreamTeam\Base\Facades\CacheHelper;
use DreamTeam\Media\Facades\RvMedia;

if(!function_exists('getImageMedia')) {
    function getImageMedia($file='',$size='', $url='')
    {
    	if ($file == '') {
    		return asset('asset_admin/image/default_image.png');
    	} else {
            $link = '';
            if (config('dreamteam_media.storage_type')  == 'local') {
                // nếu là uploads trên local thì link sẽ lấy theo localhost
                $link = '/'.config('dreamteam_media.folder');
            } else {
                // Lấy Domain theo cấu hình domain
                $domain = config('filesystems.disks.'.config('dreamteam_media.storage_type').'.domain');
                $link = $domain.'/'.config('dreamteam_media.folder');
            }
            if ($size != '') {
                if (in_array($size, array_keys(config('dreamteam_media.imageSize')))) {
                    if($url != '') $file = $url;
                    $image_array = explode('.',$file);
                    $image_name = $image_array[0];
                    $image_extension = $image_array[1] ?? '';
                    $file = $image_name.'-'.$size.'.'.$image_extension;
                }
            }
            if($url != '') return $url;
            return $link.'/'.$file;
    	}
    }
}

if (!function_exists('formatSizeUnits')) {
    function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . 'GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . 'MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . 'KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . 'B';
        } elseif ($bytes == 1) {
            $bytes = $bytes . 'B';
        } else {
            $bytes = '0B';
        }
        return $bytes;
    }
}

/**
 * Hàm xử lý uploads file cho toàn web
 * @param file          $file: file cần uploads
 * @return 
 */
if (!function_exists('uploadFile')) {
    function uploadFile($file)
    {
        if (is_file($file)) {
            try {
                $upload = RvMedia::handleUpload($file, 2, 'clients');
                if($upload['error']) {
                    return [
                        'status' => 2,
                        'message' => $upload['message'],
                    ];
                }
                return [
                    'status' => 1,
                    'message' => __('Translate::media.upload_success') . $upload['data']->name,
                    'file_info' => $upload,
                    'url' => Storage::url($upload['data']->url),
                ];
            } catch (\Exception $e) {
                Log::error($e);
                return [
                    'status' => 2,
                    'message' => __('Translate::media.upload_fail') . ': '. $e->getMessage(),
                ];
            }
        }
        return [
            'status' => 2,
            'message' => __('Translate::media.no_file_found'),
        ];
    }
}

if (! function_exists('getMediaConfig')) {
    function getMediaConfig(string $key, int|string|bool|null $default = '')
    {
        return CacheHelper::getMediaConfig($key, $default);
    }
}
