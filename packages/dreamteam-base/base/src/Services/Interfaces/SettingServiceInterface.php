<?php

namespace DreamTeam\Base\Services\Interfaces;

use DreamTeam\Base\Services\Interfaces\CrudServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

interface SettingServiceInterface  extends CrudServiceInterface
{
    /**
     * Lưu hoặc cập nhật dữ liệu cấu hình
     * @param Request $requests   $requests: dữ liệu truyền từ form lên
     * @param string $settingName : Key cấu hình
     * @param bool $hasLocale có | không đa ngôn ngữ
     * @param bool $storeLog có | không lưu log, mặc định có
     */
    public function postData(Request $requests, string $settingName, bool $hasLocale, bool $storeLog = true): void;

    /**
     * Lấy dữ liệu cấu hình theo tên và ngôn ngữ
     * @param string        $settingName: Key cấu hình
     * @param string        $locale: ngôn ngữ lấy tại config('app.language')
     */
    public function getData(string $settingName, bool $hasLocale, string $locale = null): array;
}
