<?php

namespace DreamTeam\Base\Models;
use DreamTeam\Base\Facades\Setting as SettingFacade;
use DreamTeam\Base\Models\BaseModel;
use DreamTeam\Base\Events\ClearCacheEvent;

class Setting extends BaseModel
{
	
	public $timestamps = false;

	/**
	 * Lưu hoặc cập nhật dữ liệu cấu hình
	 * @param requests 		$requests: dữ liệu truyền từ form lên
	 * @param string 		$setting_name: Key cấu hình
	 */
	public function postData($requests, $setting_name) {
		// Chuyển requests sang mảng
		$data = $requests->all();
		// Bỏ giá trị không cần thiết
		$unset = [ '_token', 'redirect', 'setLanguage' ];
		foreach ($unset as $value) {
			unset($data[$value]);
		}
		// mã hóa data
		$locale = $data['locale'] ?? getLocale();
		$data = base64_encode(json_encode($data));
		if (Setting::where('key', $setting_name)->where('locale', $locale)->exists()) {
			Setting::where('key', $setting_name)->where('locale', $locale)->update([
				'value' 	=> $data
			]);
		} else {
			Setting::insert([
				'key' 		=> $setting_name,
				'locale' 	=> $locale,
				'value' 	=> $data
			]);
		}
		// Xóa Cache setting nếu đã lưu
		\Cache::pull('setting_'.$setting_name.'_'.getLocale());
        event(new ClearCacheEvent());
	}

	/**
	 * Lấy dữ liệu cấu hình theo tên và ngôn ngữ
	 * @param string 		$setting_name: Key cấu hình
	 * @param string 		$locale: ngôn ngữ lấy tại config('app.language')
	 */
	public function getData($setting_name, $locale = null) {
		// Ngôn ngữ hiện tại
		$locale = $locale ?? getLocale();
		$option = Setting::where('key', $setting_name)->where('locale', $locale)->first();
		$data = [];
		if (!empty($option)) {
			$data = json_decode(base64_decode($option->value), true);
		}
		return $data;
	}


    /**
     * Lưu hoặc cập nhật dữ liệu cấu hình không phụ thuộc locale
     * @param requests      $requests: dữ liệu truyền từ form lên
     * @param string        $setting_name: Key cấu hình
     */
    public function postDataNoLanguge($requests, $setting_name) {
        // Chuyển requests sang mảng
        $data = $requests->all();
        // Bỏ giá trị không cần thiết
        $unset = [ '_token', 'redirect', 'setLanguage' ];
        foreach ($unset as $value) {
            unset($data[$value]);
        }
        // mã hóa data
        $data = base64_encode(json_encode($data));
        if (Setting::where('key', $setting_name)->exists()) {
            Setting::where('key', $setting_name)->update([
                'value'     => $data
            ]);
        } else {
            Setting::insert([
                'key'       => $setting_name,
                'locale'    => '',
                'value'     => $data
            ]);
        }
        event(new ClearCacheEvent());
    }

    /**
     * Lấy dữ liệu cấu hình theo tên
     * @param string        $setting_name: Key cấu hình
     */
    public function getDataNoLanguge($setting_name) {
        $option = Setting::where('key', $setting_name)->first();
        $data = [];
        if (!empty($option)) {
            $data = json_decode(base64_decode($option->value), true);
        }
        return $data;
    }

}