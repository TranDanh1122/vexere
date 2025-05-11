<?php

namespace DreamTeam\Base\Services;

use DreamTeam\Base\Repositories\Interfaces\SystemLogRepositoryInterface;
use DreamTeam\Base\Services\Interfaces\SystemLogServiceInterface;
use DreamTeam\Base\Repositories\Interfaces\SlugRepositoryInterface;
use DreamTeam\Base\Repositories\Interfaces\SeoRepositoryInterface;
use DreamTeam\Base\Repositories\Interfaces\LanguageMetaRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use DreamTeam\Base\Services\CrudService;
use DreamTeam\Base\Events\ClearCacheEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SystemLogService extends CrudService implements SystemLogServiceInterface
{
    protected SlugRepositoryInterface $slugRepository;
    protected SeoRepositoryInterface $seoRepository;
    protected LanguageMetaRepositoryInterface $langRepository;

    public function __construct(
        SystemLogRepositoryInterface $repository,
        SlugRepositoryInterface $slugRepository,
        SeoRepositoryInterface $seoRepository,
        LanguageMetaRepositoryInterface $langRepository
    )
    {
        $this->repository = $repository;
        $this->slugRepository = $slugRepository;
        $this->seoRepository = $seoRepository;
        $this->langRepository = $langRepository;
    }

    /*
     * Cập nhật lịch sử hệ thống
     * @param array         $compact: Mảng giá trị thay đổi ['key' => 'value', 'key2' => 'value2']
     * @param string        $table: tên bảng
     * @param number        $id: id của bảng
     * @param string        $idName: tên id của cột (Một vài bảng đặc biệt)
     */
    public function saveLog(string $action, array $compact = [], string $type = '', string|int $typeId = '', string $idName = 'id')
    {
        switch ($action) {
            case 'create':
                // Lấy các key từ đó đưa vào data để
                $compact_fields = [];
                foreach ($compact as $key => $value) {
                    $compact_fields[] = $key;
                }
                // foreach lấy giá trị
                $data = [];
                foreach ($compact_fields as $field) {
                    $data[$field] = (string)(is_array($compact[$field] ?? '') ? json_encode($compact[$field]) : $compact[$field]) ?? '';
                }
                // Chuẩn hóa detail
                $detail = [
                    'fields'    => $compact_fields,
                    'data'       => $data ?? [],
                ];
                $detail = base64_encode(json_encode($detail));
                $this->repository->createFromArray([
                    'admin_id'      => Auth::guard('admin')->user()->id ?? request()->user()->id,
                    'ip'            => getClientIp(),
                    'time'          => date('Y-m-d H:i:s'),
                    'action'        => $action,
                    'type'          => $type,
                    'type_id'       => $typeId,
                    'detail'        => $detail
                ]);
            break;
            case 'login':
                // Thêm logs
                $this->repository->createFromArray([
                    'admin_id'      => Auth::guard('admin')->user()->id ?? request()->user()->id,
                    'ip'            => getClientIp(),
                    'time'          => date('Y-m-d H:i:s'),
                    'action'        => $action
                ]);
            break;
            case 'delete_forever':
                $fields = array_keys($compact);
                $compact['slugTable'] = [];
                $compact['langMeta'] = [];
                $compact['metaSeo'] = [];
                $slug = $this->slugRepository->findOneFromArray([
                        'table'    => $type,
                        'table_id' => $typeId,
                    ], false);
                if($slug) {
                    $compact['slugTable'] = $slug->toArray();
                }
                $langMeta = $this->langRepository->findOneFromArray([
                        'lang_table'    => $type,
                        'lang_table_id' => $typeId,
                    ], false);
                if($langMeta) {
                    $compact['langMeta'] = $langMeta->toArray();
                }
                $metaSeo = $this->seoRepository->findOneFromArray([
                        'type'    => $type,
                        'type_id' => $typeId,
                    ], false);
                if($metaSeo) {
                    $compact['metaSeo'] = $metaSeo->toArray();
                }
                $data = ['fields' => $fields, 'old' => $compact, 'new' => []];
                $detail = base64_encode(json_encode($data));
                $this->repository->createFromArray([
                    'admin_id'      => Auth::guard('admin')->user()->id ?? request()->user()->id,
                    'ip'            => getClientIp(),
                    'time'          => date('Y-m-d H:i:s'),
                    'action'        => $action,
                    'type'          => $type,
                    'type_id'       => $typeId,
                    'detail'        => $detail
                ]);
                if($slug) {
                    $this->slugRepository->deleteByPrimary($slug->id);
                }
                if($langMeta) {
                    $this->langRepository->deleteFromWhereCondition([
                        'lang_table'    => $type,
                        'lang_table_id' => $typeId,
                    ]);
                }
                if($metaSeo) {
                    $this->seoRepository->deleteFromWhereCondition([
                        'type'    => $type,
                        'type_id' => $typeId,
                    ]);
                }
                event(new ClearCacheEvent());
            break;
            default:
                $data = \DB::table($type)->where($idName, $typeId)->first();
                // Kiểm tra nếu có tồn tại bản ghi thì mới ghi logs
                if (!empty($data)) {
                    // Lấy các key từ đó đưa vào hàm cũ và mới để check
                    $compact_fields = [];
                    foreach ($compact as $key => $value) {
                        $compact_fields[] = $key;
                    }
                    // foreach lấy giá trị cũ và mới
                    $old = []; $new = [];
                    foreach ($compact_fields as $field) {
                        if ($field != 'updated_at') {
                            $old[$field] = (string)$data->$field ?? '';
                            $new[$field] = (string)(is_array($compact[$field] ?? '') ? json_encode($compact[$field]) : $compact[$field]) ?? '';
                        }
                    }
                    // nếu giá trị cũ khác giá trị mới thì mới thêm
                    if ($old != $new) {
                        // Thêm updated_at vào nếu có tại compact
                        if (in_array('updated_at', $compact_fields)) {
                            $old['updated_at'] = (string)$data->$field ?? '';
                            $new['updated_at'] = (string)(is_array($compact[$field] ?? '') ? json_encode($compact[$field]) : $compact[$field]) ?? '';
                        }
                        // Chuẩn hóa detail
                        $detail = [
                            'fields'    => $compact_fields,
                            'old'       => $old ?? [],
                            'new'       => $new ?? [],
                        ];
                        $detail = base64_encode(json_encode($detail));
                        // Thêm logs
                        $this->repository->createFromArray([
                            'admin_id'      => Auth::guard('admin')->user()->id ?? request()->user()->id,
                            'ip'            => getClientIp(),
                            'time'          => date('Y-m-d H:i:s'),
                            'action'        => $action,
                            'type'          => $type,
                            'type_id'       => $typeId,
                            'detail'        => $detail
                        ]);
                    }
                }
            break;
        }
    }

    public function deleteWithRequest(Request $request)
    {
        return $this->repository->deleteWithRequest($request);
    }
}
