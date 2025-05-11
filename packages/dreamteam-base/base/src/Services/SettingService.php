<?php

namespace DreamTeam\Base\Services;

use DreamTeam\Base\Repositories\Interfaces\SettingRepositoryInterface;
use DreamTeam\Base\Services\Interfaces\SettingServiceInterface;
use DreamTeam\Base\Services\CrudService;
use DreamTeam\Base\Events\ClearCacheEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DreamTeam\Base\Enums\SystemLogStatusEnum;
use DreamTeam\Base\Models\Setting;
use DreamTeam\Base\Services\Interfaces\SystemLogServiceInterface;

class SettingService extends CrudService implements SettingServiceInterface
{

    public function __construct(
        SettingRepositoryInterface $repository
    )
    {
        $this->repository = $repository;
    }

    /**
     * Lưu hoặc cập nhật dữ liệu cấu hình
     * @param Request $requests   $requests: dữ liệu truyền từ form lên
     * @param string $settingName : Key cấu hình
     * @param bool $hasLocale có | không đa ngôn ngữ
     * @param bool $storeLog có | không lưu log, mặc định có
     */
    public function postData(Request $requests, string $settingName, bool $hasLocale, bool $storeLog = true): void
    {
        $data = $requests->all();
        $unset = [ '_token', 'redirect', 'setLanguage' ];
        foreach ($unset as $value) {
            unset($data[$value]);
        }
        $conditions = [
            'key' => $settingName
        ];
        $locale = '';
        if ($hasLocale) {
            $locale = $data['locale'] ?? $requests->lang_locale ?? getLocale();
            $conditions['locale'] = $locale;
        }
        $dataInsert = base64_encode(json_encode($data));
        $checkExits = $this->repository->findOneFromArray($conditions, false);
        if ($checkExits) {
            $old = [
                ...(json_decode(base64_decode($checkExits->value), 1)),
                'key' => $settingName,
                'lang_locale' => $locale,
                'has_locale'  => $hasLocale
            ];
            $new = [
                ...$data,
                'key' => $settingName,
                'lang_locale' => $locale,
                'has_locale'  => $hasLocale
            ];
            $detail = [
                'fields'    => array_unique(array_keys($old) + array_keys($new)),
                'old'       => $old ?? [],
                'new'       => $new ?? [],
            ];
            $detail = base64_encode(json_encode($detail));
            // Thêm logs
            if ($storeLog) {
                app(SystemLogServiceInterface::class)->create([
                    'admin_id'      => Auth::guard('admin')->user()->id,
                    'ip'            => getClientIp(),
                    'time'          => date('Y-m-d H:i:s'),
                    'action'        => SystemLogStatusEnum::UPDATE,
                    'type'          => (new Setting())->getTable(),
                    'type_id'       => 0,
                    'setting_key'   => $settingName,
                    'detail'        => $detail
                ]);
            }
            $this->repository->updateFromWhereConditions($conditions, ['value' => $dataInsert]);
        } else {
            $this->repository->insertMultipleFromArray([
                'key'       => $settingName,
                'locale'    => $locale,
                'value'     => $dataInsert
            ]);
        }
        event(new ClearCacheEvent());
    }

    /**
     * Lấy dữ liệu cấu hình theo tên và ngôn ngữ
     * @param string        $settingName: Key cấu hình
     * @param string        $locale: ngôn ngữ lấy tại config('app.language')
     */
    public function getData(string $settingName, bool $hasLocale, string $locale = null): array
    {
        $conditions = [
            'key' => $settingName
        ];
        if ($hasLocale) {
            $conditions['locale'] = $locale ?? $requests->lang_locale ?? getLocale();
        }
        $option = $this->repository->findOneFromArray($conditions, false);
        $data = [];
        if (!empty($option)) {
            $data = json_decode(base64_decode($option->value), true);
        }
        return $data;
    }

}
