<?php

namespace DreamTeam\SyncLink\Http\Controllers;
use DreamTeam\Base\Http\Controllers\AdminController;

use Illuminate\Http\Request;
use ListData;
use Form;
use ListCategory;
use Illuminate\Http\JsonResponse;
use DreamTeam\SyncLink\Models\SyncLink;
use DreamTeam\SyncLink\Services\Interfaces\SyncLinkServiceInterface;
use DreamTeam\Base\Enums\BaseStatusEnum;
use DreamTeam\Base\Services\Interfaces\BaseServiceInterface;
use DreamTeam\Base\Services\Interfaces\SystemLogServiceInterface;
use DreamTeam\Base\Enums\SystemLogStatusEnum;
use DreamTeam\SyncLink\Enums\SyncLinkEnum;
use DreamTeam\SyncLink\Http\Requests\SyncLinkRequest;

class SyncLinkController extends AdminController
{
    protected SyncLinkServiceInterface $syncLinkService;
    protected BaseServiceInterface $baseService;
    protected SystemLogServiceInterface $systemLogService;
    protected array $code;
    
    function __construct(
        SyncLinkServiceInterface $syncLinkService,
        BaseServiceInterface $baseService,
        SystemLogServiceInterface $systemLogService
    )
    {
        $this->table_name = (new SyncLink)->getTable();
        $this->module_name = 'SyncLink::admin.name';
        $this->has_seo = false;
        $this->has_locale = false;
        $this->syncLinkService = $syncLinkService;
        $this->baseService = $baseService;
        $this->systemLogService = $systemLogService;
        parent::__construct();

        $this->code = SyncLinkEnum::labels();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $requests) {
        $listdata = new ListData(
            $requests,
            $this->syncLinkService,
            $this->table_name,
            'SyncLink::table',
            [],
            true,
            $this->has_locale,
            30,
            [ $this->table_name.'.id' => 'desc'
        ]);
        $code = $this->code;
        // Build Form tìm kiếm
        $listdata->search('old', __('SyncLink::admin.source_link'), 'string');
        // Build các hành động
        $listdata->action('status');
        if (checkRole($this->table_name.'_import')) {
            $actionImport = \View('SyncLink::import_file')->render();
            $listdata->topAction($actionImport);
        }
        if (checkRole($this->table_name.'_export')) {
            $listdata->searchBtn(__('SyncLink::admin.export_name'), route('admin.ajax.sync_links.export'), 'primary', 'fas fa-file-excel');
        }
        $listdata->add('old', __('SyncLink::admin.source_link'));
        $listdata->add('new', __('SyncLink::admin.target_link'));
        $listdata->add('', __('SyncLink::admin.code'), 0,);
        $listdata->add('status', __('Core::admin.general.status'), 0, 'status');
        $listdata->add('', __('Core::admin.general.action'), 0, 'action');
        return $listdata->render();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   
        // Khởi tạo form
        $form = new Form;
        $form->card('col-lg-12', __('SyncLink::admin.create'), __('SyncLink::admin.desc'));
            $form->text('old', '', 1,  __('SyncLink::admin.source_link'), '');
            $form->text('new', '', 1,  __('SyncLink::admin.target_link'), '');
            $form->radio('code', SyncLinkEnum::TEMPORARY, __('SyncLink::admin.import.code'), $this->code, 'col-lg-6');
            $form->checkbox('status', BaseStatusEnum::ACTIVE, BaseStatusEnum::ACTIVE,  __('Core::admin.general.status'), 'col-lg-6');
            $form->actionInline('add');
        $form->endCard();
        // Hiển thị form tại view
        return $form->render('create_and_show');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \DreamTeam\SyncLink\Http\Requests\SyncLinkRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SyncLinkRequest $requests)
    {
        $status = BaseStatusEnum::DEACTIVE;
        extract($requests->all(), EXTR_OVERWRITE);
        $created_at = $updated_at = date('Y-m-d H:i:s');
        $compact = compact('old', 'new', 'code', 'status');
        \DB::beginTransaction();
        try {
            $syncLink = $this->syncLinkService->create($compact);
            $this->baseService->handleRelatedRecord($requests, $this->table_name, $syncLink->id, $this->has_seo, $this->has_locale, true, SystemLogStatusEnum::CREATE, $compact, false);
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::debug('Store ' . $this->table_name . ' error: '.$e->getMessage());
            return redirect()->back()->withErrors(__('Translate::admin.error_message_catch'));
        }
        \DB::commit();
        return redirect(route('admin.'.$this->table_name.'.'.$redirect, $syncLink->id))->with([
            'type' => 'success',
            'message' => __('Translate::admin.create_success')
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(SyncLink $syncLink) {
        $form = new Form;
        $form->card('col-lg-12', __('SyncLink::admin.create'), __('SyncLink::admin.desc'));
            $form->text('old', $syncLink->old, 0, __('SyncLink::admin.source_link'));
            $form->text('new', $syncLink->new, 0,  __('SyncLink::admin.target_link'));
            $form->radio('code', $syncLink->code, __('SyncLink::admin.import.code'), $this->code, 'col-lg-6');
            $form->checkbox('status', $syncLink->status, BaseStatusEnum::ACTIVE, __('Core::admin.general.status'), 'col-lg-6');
            $form->actionInline('edit');
        $form->endCard();
        $id = $syncLink->id;
        return $form->render('edit_and_show', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \DreamTeam\SyncLink\Http\Requests\SyncLinkRequest  $requests
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SyncLinkRequest $requests, $id) {
        $this->syncLinkService->read($id);
        $status = BaseStatusEnum::DEACTIVE;
        extract($requests->all(), EXTR_OVERWRITE);
        $compact = compact('old', 'new', 'code', 'status');
        \DB::beginTransaction();
        try {
            $this->baseService->handleRelatedRecord($requests, $this->table_name, $id, $this->has_seo, $this->has_locale, true, SystemLogStatusEnum::UPDATE, $compact, false);
            $this->syncLinkService->update($id, $compact);
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::debug('Update ' . $this->table_name . ' error: '.$e->getMessage());
            return redirect()->back()->withErrors(__('Translate::admin.error_message_catch'));
        }
        \DB::commit();
        return redirect(route('admin.'.$this->table_name.'.'.$redirect, $id))->with([
            'type' => 'success',
            'message' => __('Translate::admin.update_success')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Thêm dữ liệu từ excel
     */
    public function import(Request $requests) {
        if (!checkRole($this->table_name.'_import')) {
            return [
                'status' => 2,
                'message' => __('Core::admin.no_permission')
            ];
        }
        if ($requests->hasFile('files')) {
            $file = $requests->file('files');
            // Lấy thông tin file
            $file_info = pathinfo($file->getClientOriginalName());
            // Phần mở rộng
            $file_extension = $file_info['extension'];
            $allow_extension = ['xlsx','xls'];
            if (in_array($file_extension, $allow_extension)) {
                try {
                    \Excel::import(new \DreamTeam\SyncLink\Imports\SyncLinkImport($this->syncLinkService), $file);
                    return [
                        'status' => 1,
                        'message' => __('Translate::admin.create_success')
                    ];
                } catch (\Exception $e) {
                    \Log::error($e);
                    return [
                        'status' => 2,
                        'message' => __('Translate::admin.error_message_catch')
                    ];
                }
            } else {
                return [
                    'status' => 2,
                    'message' => __('SyncLink::admin.import.file_specifix')
                ];
            }
        } else {
            return [
                'status' => 2,
                'message' => __('Translate::admin.ajax_error_edit')
            ];
        }
    }

    /**
     * Xuất dữ liệu excel với điều kiện bộ lọc
     */
    public function export(Request $requests) {
        if (!checkRole($this->table_name.'_export')) {
            return redirect()->back()->withErrors([
                'type' => 'danger',
                'message' => __('Core::admin.no_permission')
            ]);
        }
        extract($requests->all(), EXTR_OVERWRITE);
        $conditions = [];
        if (isset($old) && $old != '') {
            $conditions['old'] = ['LIKE' => $old];
        }
        // Link mới
        if (isset($new) && $new != '') {
            $conditions['new'] = ['LIKE' => $new];
        }
        // lọc trạng thái
        if (isset($code) && $code != '') {
            $conditions['code'] = ['=' => $code];
        }
        // lọc trạng thái
        if (isset($status) && $status != '') {
            $conditions['status'] = ['=' => $status];
        } else {
            $conditions['status'] = ['DFF' => BaseStatusEnum::DELETE];
        }

        // Mảng export
        $data = [
            'file_name' => 'sync-links-'.time(),
            'data' => [
                // 
            ]
        ];
        $datas = $this->syncLinkService->getWithMultiFromConditions([], $conditions, 'id', 'desc');
        // Foreach lấy mảng data
        foreach ($datas as $key => $value) {
            $data['data'][] = [
                $value->old,
                $value->new,
                $value->code,
            ];
        }
        return \Excel::download(new \DreamTeam\Base\Export\GeneralExports($data), $data['file_name'].'.xlsx');
    }

    public function deleteForever(Request $requests, $id): JsonResponse
    {
        if (!checkRole($this->table_name.'_delete')) {
            return response()->json([
                'status' => 2,
                'message' => __('Core::admin.no_permission')
            ]);
        }
        \DB::beginTransaction();
        $idArray = $requests->idArray ?? [];
        try {
            if(count($idArray) > 0) {
                foreach($idArray as $id) {
                    $this->handleDeleteForever($id);
                }
            } else {
                $this->handleDeleteForever($id);
            }
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error('Delete forever sync_links error :'. $e->getMessage());
            return response()->json([
                'status' => 2,
                'message' => __('Translate::admin.error_message_catch')
            ]);
        }
        \DB::commit();
        return response()->json([
            'status' => 1,
            'message' => __('Core::admin.delete_success')
        ]);
    }

    private function handleDeleteForever($id)
    {
        $checkTrashRecord = $this->syncLinkService->findOne([
                'id' => $id,
                'status' => BaseStatusEnum::DELETE
            ], true);
        $data = $checkTrashRecord->toArray();
        $this->systemLogService->saveLog(SystemLogStatusEnum::DELETE_FOREVER, $data, $this->table_name, $id);
        $this->syncLinkService->deleteFromWhereCondition([
                'id' => $id,
                'status' => BaseStatusEnum::DELETE
            ]);
    }

}
