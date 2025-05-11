<?php

namespace DreamTeam\AdminUser\Http\Controllers;

use Illuminate\Http\Request;
use ListData;
use Form;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use DreamTeam\Base\Http\Controllers\AdminController;
use DreamTeam\AdminUser\Models\AdminUserRole;
use DreamTeam\Base\Enums\BaseStatusEnum;
use DreamTeam\AdminUser\Http\Requests\AdminUserRoleRequest;
use DreamTeam\Base\Enums\SystemLogStatusEnum;
use DreamTeam\Base\Services\Interfaces\SystemLogServiceInterface;
use DreamTeam\AdminUser\Services\Interfaces\AdminUserRoleServiceInterface;
use DreamTeam\AdminUser\Services\Interfaces\AdminUserServiceInterface;
use DreamTeam\Base\Services\Interfaces\BaseServiceInterface;

class AdminUserRoleController extends AdminController
{
    protected AdminUserRoleServiceInterface $adminUserRoleService;
    protected AdminUserServiceInterface $adminUserService;
    protected BaseServiceInterface $baseService;
    protected SystemLogServiceInterface $systemLogService;

    function __construct(
        AdminUserRoleServiceInterface $adminUserRoleService,
        AdminUserServiceInterface $adminUserService,
        BaseServiceInterface $baseService,
        SystemLogServiceInterface $systemLogService
    ) {
        $this->table_name = (new AdminUserRole)->getTable();
        $this->module_name = 'AdminUser::admin.roles.name';
        $this->has_seo = false;
        $this->has_locale = false;
        $this->adminUserRoleService = $adminUserRoleService;
        $this->adminUserService = $adminUserService;
        $this->baseService = $baseService;
        $this->systemLogService = $systemLogService;
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
            $this->adminUserRoleService,
            $this->table_name,
            'AdminUser::admin_user_roles.table',
            []
        );
        // Build Form tìm kiếm
        $listdata->search('name', __('Core::admin.general.name'), 'string');
        $listdata->search('created_at', __('Core::admin.general.created_at'), 'range');
        $listdata->search('status', __('Core::admin.general.status'), 'array', BaseStatusEnum::tableLabels());
        // Build các hành động
        $listdata->action('status');
        // Build các btn hành động
        $listdata->btnAction('status', __('Translate::table.apply'), 'primary');
        // Build bảng
        $listdata->add('name', __('Core::admin.general.name'), 1);
        // $listdata->add('team', __('AdminUser::admin.roles.team'), 1);
        $listdata->add('', __('AdminUser::admin.permision'));
        $listdata->add('status', __('Core::admin.general.status'), 1, 'status');
        $listdata->add('action', __('Core::admin.general.action'), 0, 'action');

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
        $form->row();
        $form->card('col-lg-12', __('AdminUser::admin.roles.create_group'));
        $form->text('name', '', 1, __('Core::admin.general.name'), '', false);
        $form->checkbox('status', BaseStatusEnum::ACTIVE, BaseStatusEnum::ACTIVE, __('Core::admin.general.status'), 'col-lg-12');
        $form->endCard();
        $form->endRow();
        $form->card('col-lg-12');
        $form->custom('AdminUser::admin_users.role', ['name' => __('AdminUser::admin.roles.name'), 'required' => true]);
        $form->endCard();
        $form->action('add');
        // Hiển thị form tại view
        return $form->render('create_multi_col');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \DreamTeam\AdminUser\Http\Requests\AdminUserRoleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminUserRoleRequest $requests)
    {
        $status = BaseStatusEnum::DEACTIVE;
        $team = '';
        $redirect = 'edit';
        $created_at = $updated_at = date('Y-m-d H:i:s');
        extract($requests->all(), EXTR_OVERWRITE);
        $permisions = json_encode($role ?? []);
        if ($redirect == 'save') {
            $status = BaseStatusEnum::DRAFT;
            $redirect = 'edit';
        }
        if ($redirect == 'exit') {
            $redirect = 'index';
        }
        $compact = compact('name', 'permisions', 'status', 'created_at', 'updated_at');
        DB::beginTransaction();
        try {
            $adminUserRole = $this->adminUserRoleService->create($compact);
            $this->baseService->handleRelatedRecord($requests, $this->table_name, $adminUserRole->id, $this->has_seo, $this->has_locale, true, SystemLogStatusEnum::CREATE, $compact, false);
        } catch (\Exception $e) {
            DB::rollback();
            Log::debug('Store ' . $this->table_name . ' error: ' . $e->getMessage());
            return redirect()->back()->withErrors(__('Translate::admin.error_message_catch'));
        }
        DB::commit();
        return redirect(route('admin.' . $this->table_name . '.' . $redirect, $adminUserRole->id))->with([
            'type' => 'success',
            'message' => __('Core::admin.create_success')
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
        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(AdminUserRole $adminUserRole)
    {
        $form = new Form;
        $form->row();
        $form->card('col-lg-12', __('AdminUser::admin.roles.create_group'));
        $form->text('name', $adminUserRole->name, 1, __('Core::admin.general.name'), '', false);
        $form->checkbox('status', $adminUserRole->status, BaseStatusEnum::ACTIVE, __('Core::admin.general.status'), 'col-lg-12');
        $form->endCard();
        $form->card('col-lg-12');
        $form->custom('AdminUser::admin_users.role', [
            'name' => __('AdminUser::admin.roles.name'),
            'required' => true,
            'roleName' => 'permisions',
            'data_edit' => $adminUserRole
        ]);
        $form->endCard();
        $form->endRow();
        $form->action('edit');
        $id = $adminUserRole->id;
        $data_edit = $adminUserRole;

        // Hiển thị form tại view
        return $form->render('edit_multi_col', compact('id', 'data_edit'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \DreamTeam\AdminUser\Http\Requests\AdminUserRoleRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AdminUserRoleRequest $requests, $id)
    {
        $status = BaseStatusEnum::DEACTIVE;
        extract($requests->all(), EXTR_OVERWRITE);
        $permisions = json_encode($role);
        $updated_at = date('Y-m-d H:i:s');
        $compact = compact('name', 'permisions', 'status', 'updated_at');
        DB::beginTransaction();
        try {
            $this->baseService->handleRelatedRecord($requests, $this->table_name, $id, $this->has_seo, $this->has_locale, true, SystemLogStatusEnum::UPDATE, $compact, false);
            $this->adminUserRoleService->update($id, $compact);
        } catch (\Exception $e) {
            DB::rollback();
            Log::debug('Update ' . $this->table_name . ' error: ' . $e->getMessage());
            return redirect()->back()->withErrors(__('Translate::admin.error_message_catch'));
        }
        DB::commit();
        return redirect(route('admin.' . $this->table_name . '.' . $redirect, $id))->with([
            'type' => 'success',
            'message' => __('Core::admin.update_success')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteForever($id, Request $requests)
    {
        if (!checkRole($this->table_name . '_deleteForever')) {
            return response()->json([
                'status' => 2,
                'message' => __('Core::admin.no_permission')
            ]);
        }
        DB::beginTransaction();
        $idArray = $requests->idArray ?? [];
        try {
            if (count($idArray) > 0) {
                foreach ($idArray as $id) {
                    $this->handleDeleteForever($id);
                }
            } else {
                $this->handleDeleteForever($id);
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Delete forever ' . $this->table_name . ' error :' . $e->getMessage());
            return response()->json([
                'status' => 2,
                'message' => __('Translate::admin.error_message_catch')
            ]);
        }
        DB::commit();
        return response()->json([
            'status' => 1,
            'message' => __('Core::admin.delete_success')
        ]);
    }

    private function handleDeleteForever($id)
    {
        $checkTrashRecord = $this->adminUserRoleService->findOne([
            'id' => $id,
            'status' => BaseStatusEnum::DELETE
        ], true);
        $data = $checkTrashRecord->toArray();
        $this->adminUserService->updateFromConditions(
            ['admin_user_role_id' => $id],
            ['admin_user_role_id' => 0]
        );
        $this->systemLogService->saveLog(SystemLogStatusEnum::DELETE_FOREVER, $data, $this->table_name, $id);
        $this->adminUserRoleService->deleteFromWhereCondition([
            'id' => $id,
            'status' => BaseStatusEnum::DELETE
        ]);
    }
}
