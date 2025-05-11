<?php

namespace DreamTeam\Base\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use ListData;
use DreamTeam\Base\Models\SystemLog;
use DreamTeam\Base\Enums\BaseStatusEnum;
use DreamTeam\Base\Enums\SystemLogStatusEnum;
use DreamTeam\Base\Services\Interfaces\LanguageMetaServiceInterface;
use DreamTeam\Base\Services\Interfaces\SystemLogServiceInterface;
use DreamTeam\Base\Services\Interfaces\SlugServiceInterface;
use DreamTeam\Base\Services\Interfaces\SeoServiceInterface;
use DreamTeam\Base\Services\Interfaces\MenuServiceInterface;

class SystemLogController extends AdminController
{
    protected LanguageMetaServiceInterface $langMetaService;
    protected SystemLogServiceInterface $systemLogService;
    protected SlugServiceInterface $slugService;
    protected SeoServiceInterface $seoService;
    protected MenuServiceInterface $menuService;

	function __construct(
        LanguageMetaServiceInterface $langMetaService,
        SystemLogServiceInterface $systemLogService,
        SlugServiceInterface $slugService,
        SeoServiceInterface $seoService,
        MenuServiceInterface $menuService
    )
    {
        $this->table_name = (new SystemLog)->getTable();
        $this->module_name = 'Core::admin.admin_menu.system_logs';
        $this->has_seo = false;
        $this->langMetaService = $langMetaService;
        $this->systemLogService = $systemLogService;
        $this->slugService = $slugService;
        $this->seoService = $seoService;
        $this->menuService = $menuService;
        parent::__construct();
    }

	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $requests)
    {
        $listdata = new ListData(
            $requests,
            $this->systemLogService,
            $this->table_name,
            'Core::system_logs.table',
            [],
            false,
            false,
            30,
            [ $this->table_name.'.id' => 'desc' ]
        );
        $tableNames = [];
        foreach (config('DreamTeamModule.modules') as $key => $value) {
            $tableNames[$key] = $value['name'];
        }
        // Build Form tìm kiếm
        $admin_users = \DreamTeam\AdminUser\Models\AdminUser::get()->pluck('name', 'id');
        $listdata->search('admin_id', 'Core::admin.logs.admin_id', 'array', $admin_users);
        $listdata->search('type', 'Core::admin.logs.type', 'array', $tableNames);
        $listdata->search('type_name', 'Core::admin.logs.type_name_search', 'custom_conditions');
        $listdata->search('action', 'Core::admin.logs.action', 'array', SystemLogStatusEnum::labels());
        $listdata->search('time', 'Core::admin.logs.time', 'range');
        if (checkRole($this->table_name.'_delete')) {
            $listdata->searchBtn( __('Core::admin.general.delete'), route('admin.system_logs.deleteWithRequest'), 'danger delete-job-status form-submit-confirm', 'fas fa-trash');
        }
        // Build bảng
        $listdata->add('admin_id', 'Core::admin.logs.admin_id', 0);
        $listdata->add('ip', 'Core::admin.logs.ip', 0);
        $listdata->add('action', 'Core::admin.logs.action', 0);
        $listdata->add('type', 'Core::admin.logs.type', 0);
        $listdata->add('time', 'Core::admin.logs.time', 0);
        $listdata->add('', 'Core::admin.general.show', 0, 'show');

        $listdata->no_add();
        $listdata->no_trash();
        $include_view_bottom = [
            'Core::system_logs.script_index' => []
        ];
        return $listdata->render(compact('admin_users', 'include_view_bottom'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $requests) {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(SystemLog $systemLog)
    {
        $admin_users = \DreamTeam\AdminUser\Models\AdminUser::where('id', $systemLog->admin_id)->first();
    	return view('Core::'.$this->table_name.'.show', compact(
            'systemLog', 'admin_users'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $requests, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
    	//
    }

    public function deleteWithRequest(Request $requests) {
        if (!checkRole($this->table_name.'_delete')) {
            return redirect()->back()->withErrors(__('Core::admin.no_permission'));
        }
        \DB::beginTransaction();
        try {
            $this->systemLogService->deleteWithRequest($requests);
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error('Delete system_logs error: '. $e->getMessage());
            return redirect()->back()->withErrors(__('Translate::admin.error_message_catch'));
        }
        \DB::commit();
        return redirect()->back()->with([
            'type' => 'success',
            'message' => __('Translate::admin.delete_success')
        ]);
    }

    public function rollback($id) {
        if (!checkRole($this->table_name.'_restore')) {
            return redirect()->back()->withErrors(__('Core::admin.no_permission'));
        }
        $systemLog = $this->systemLogService->findOne(['id' => $id, 'action' => SystemLogStatusEnum::DELETE_FOREVER], true);
        \DB::beginTransaction();
        try {
            $data = $systemLog->getDetail();
            $type = $systemLog->type ?? '';
            $typeID = $systemLog->type_id;
            $dataOld = $data['old'] ?? [];

            $dataOld['status'] = 1;
            if (isset($dataOld['google_index']) && is_array($dataOld['google_index'])) {
                $dataOld['google_index'] = $dataOld['google_index']['value'] ?? 'na';
            }
            $slugTable = $dataOld['slugTable'] ?? [];
            $langMeta = $dataOld['langMeta'] ?? [];
            $metaSeo = $dataOld['metaSeo'] ?? [];
            unset($dataOld['slugTable']);
            unset($dataOld['langMeta']);
            unset($dataOld['metaSeo']);

            if (isset($dataOld['created_at'])) {
                $dataOld['created_at'] = date('Y-m-d H:i:s', strtotime($dataOld['created_at']));
            }
            if (isset($dataOld['updated_at'])) {
                $dataOld['updated_at'] = date('Y-m-d H:i:s', strtotime($dataOld['updated_at']));
            }
            if (isset($dataOld['comments'])) {
                $comments = $dataOld['comments'] ?? [];
                unset($dataOld['comments']);
                if(count($comments) && \Schema::hasTable('comments')) {
                    $comments = formatDataSystermLog($comments);
                    \DB::table('comments')->insert($comments);
                }
            }
            if ($type == 'menus') {
                $this->menuService->insert($dataOld);
            } else {
                $response = apply_filters(ROLLBACK_DATA_FROM_LOG, null, $type, $dataOld);
                if (!isset($response['success']) || $response['success'] == false) {
                    return redirect()->back()->withErrors(__('Core::admin.no_record_rollback'));
                }
                if (isset($response['type'])) { 
                    $type = $response['type']; 
                }
                if (isset($response['typeID'])) { 
                    $typeID = $response['typeID']; 
                }
            }
            if(count($slugTable)) {
                $slugTable['created_at'] = date('Y-m-d H:i:s', strtotime($slugTable['created_at']));
                $slugTable['updated_at'] = date('Y-m-d H:i:s', strtotime($slugTable['updated_at']));
                $this->slugService->insert($slugTable);
            }
            if(count($langMeta)) {
                $this->langMetaService->insert($langMeta);
            }
            if(count($metaSeo)) {
                $this->seoService->insert($metaSeo);
            }
            $this->systemLogService->deleteFromWhereCondition(['id' => $systemLog->id, 'action' => SystemLogStatusEnum::DELETE_FOREVER]);
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error('Rollback SystemLog Error');
            \Log::error($e);
            \Log::error($e->getMessage());
            return redirect()->back()->withErrors(__('Translate::admin.error_message_catch').' - Message: '.$e->getMessage());
        }
        \DB::commit();
        return redirect(route('admin.'.$type.'.edit', $typeID ?? $systemLog->type_id));
    }
}
