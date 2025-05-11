<?php

namespace DreamTeam\AdminUser\Http\Controllers;

use Illuminate\Http\Request;
use ListData;
use Form;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use DreamTeam\Base\Http\Controllers\AdminController;
use DreamTeam\AdminUser\Models\AdminUser;
use DreamTeam\AdminUser\Services\Interfaces\AdminUserServiceInterface;
use DreamTeam\AdminUser\Services\Interfaces\AdminUserRoleServiceInterface;
use DreamTeam\Base\Enums\BaseStatusEnum;
use DreamTeam\AdminUser\Http\Requests\AdminUserRequest;
use DreamTeam\Base\Enums\SystemLogStatusEnum;
use DreamTeam\Base\Services\Interfaces\BaseServiceInterface;
use DreamTeam\Base\Services\Interfaces\LanguageMetaServiceInterface;
use DreamTeam\Base\Services\Interfaces\SystemLogServiceInterface;
use DreamTeam\Base\Services\Interfaces\SlugServiceInterface;
use Illuminate\Support\Str;

class AdminUserController extends AdminController
{
    protected AdminUserServiceInterface $adminUserService;
    protected AdminUserRoleServiceInterface $adminUserRoleService;
    protected BaseServiceInterface $baseService;
    protected LanguageMetaServiceInterface $langMetaService;
    protected SystemLogServiceInterface $systemLogService;
    protected SlugServiceInterface $slugService;
    protected int $countNumber;
    protected bool $checkNumber;
    protected array $roleGroups;
    protected array $rolePermisions;

    function __construct(
        AdminUserServiceInterface $adminUserService,
        AdminUserRoleServiceInterface $adminUserRoleService,
        BaseServiceInterface $baseService,
        LanguageMetaServiceInterface $langMetaService,
        SystemLogServiceInterface $systemLogService,
        SlugServiceInterface $slugService
    ) {
        $this->table_name = (new AdminUser)->getTable();
        $this->module_name = 'AdminUser::admin.admin_user_name';
        $this->has_seo = false;
        $this->has_locale = false;
        parent::__construct();
        $this->countNumber = 0;
        $this->checkNumber = false;
        $this->adminUserService = $adminUserService;
        $this->adminUserRoleService = $adminUserRoleService;
        $this->baseService = $baseService;
        $this->langMetaService = $langMetaService;
        $this->systemLogService = $systemLogService;
        $this->slugService = $slugService;
        $themeValidate = getOption('theme_validate', 'all', false);
        if (($themeValidate['type'] ?? '') == 'limited' && ($themeValidate['package'] ?? '') == 'base') {
            $this->checkNumber = true;
            $this->countNumber = $this->adminUserService->search()->count();
        }
        $adminUserRoles = $this->adminUserRoleService->search(['status' => BaseStatusEnum::ACTIVE]);
        $this->roleGroups = $adminUserRoles->pluck('name', 'id')->toArray();
        $this->rolePermisions = $adminUserRoles->pluck('permisions', 'id')->toArray();
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
            $this->adminUserService,
            $this->table_name,
            'AdminUser::admin_users.table',
            []
        );
        $option = getOption('googleAuthenticate', '', false);
        $isEnabled = $option['enabled'] ?? 0;
        // Build Form tìm kiếm
        $listdata->search('name', __('Core::admin.general.name'), 'string');
        $listdata->search('email', 'Email', 'string');
        $listdata->search('admin_user_role_id', __('AdminUser::admin.roles.name'), 'array', $this->roleGroups);
        $listdata->search('created_at', __('Core::admin.general.created_at'), 'range');
        $listdata->search('status', __('Core::admin.general.status'), 'array', BaseStatusEnum::tableLabels());
        // Build các hành động
        $listdata->action('status');
        $listdata->no_trash();
        // Build các btn hành động
        $listdata->btnAction('status', __('Translate::table.apply'), 'primary');
        // Build bảng
        $listdata->add('image', 'Core::admin.general.image', 0, 'image');
        $listdata->add('name', __('Core::admin.general.name'), 1);
        $listdata->add('email', 'Email', 1);
        $listdata->add('admin_user_role_id', __('AdminUser::admin.roles.name'));
        if ($isEnabled) {
            $listdata->add('enabel_google2fa', __('2FA'), 0);
        }
        $listdata->add('status', __('Core::admin.general.status'), 1, '');
        $listdata->add('edit', __('Core::admin.general.edit'), 0, 'edit');
        $listdata->add('', __('Core::admin.general.delete'), 0, '');

        return $listdata->render(compact('isEnabled'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if ($this->countNumber >= 3 && $this->checkNumber) {
            return redirect()->back()->withErrors('AdminUser::admin.limit_number');
        }
        // Khởi tạo form
        $form = new Form;
        $form->row();
        $form->col('col-lg-6');
            $form->card();
                $form->head(__('AdminUser::admin.story'));
                $form->row();
                    $form->text('name', '', 1, __('AdminUser::admin.username'), '', false, 'col-lg-6', '', 191);
                    $form->email('email', '', 1, 'Email', '', false, 'col-lg-6');
                $form->endRow();
                $form->row();
                    $form->passwordGenerate('password', '', 1, __('AdminUser::admin.login.password'), '', '', false, 'col-lg-6');
                    $form->password('password_confirm', '', 1, __('AdminUser::admin.forgot_password.password_comfirm'), '', 'password', false, 'col-lg-6');
                $form->endRow();
                $form->head(__('AdminUser::admin.add_info'));
                    $form->image('avatar', '', 0, __('Core::admin.general.avatar'), '', '');
                    $form->row();
                        $form->text('display_name', '', 0, __('AdminUser::admin.name'), '', false, 'col-lg-6');
                        $form->text('position', '', 0, __('AdminUser::admin.position'), '', false, 'col-lg-6');
                    $form->endRow();
                    $form->text('phone', '', 1, __('AdminUser::admin.phone'), '', false, 'col-lg-12');
                    $form->textarea('summary', '', 0, __('AdminUser::admin.summary'), __('AdminUser::admin.summary_placeholder'), false, '', '', false, 200);
                    $form->note(__('AdminUser::admin.summary_note'));
            $form->endCard();
            $form->head(__('AdminUser::admin.link_internet'));
                $form->card();
                    $form->row();
                    $form->text('website', '', 0, 'Website', '', false, 'col-lg-6');
                    $form->text('facebook', '', 0, 'Facebook', '', false, 'col-lg-6');
                    $form->endRow();
                    $form->row();
                    $form->text('twitter', '', 0, 'Twitter', '', false, 'col-lg-6');
                    $form->text('pinterest', '', 0, 'Pinterest', '', false, 'col-lg-6');
                    $form->endRow();
                    $form->row();
                    $form->text('instagram',  '', 0, 'Instagram', '', false, 'col-lg-6');
                    $form->text('youtube', '', 0, 'Youtube', '', false, 'col-lg-6');
                    $form->endRow();
                    $form->ckeditor('infomation', '', 0, __('AdminUser::admin.author_desc'), false, '', '', true);
                    $form->tableOption('FILTER_RENDER_FORM_OPTION_ADMIN_USER', $this->table_name);
                $form->endCard();
            $form->endCol();
            $form->col('col-lg-6');
                $form->card('', __('AdminUser::admin.role_title'));
                $form->checkbox('status', BaseStatusEnum::ACTIVE, BaseStatusEnum::ACTIVE, __('Core::admin.general.status'), 'col-lg-12');
                    $form->select('admin_user_role_id', '', 0, __('AdminUser::admin.roles.select_role'), ['' => __('AdminUser::admin.roles.select_role')] + ['spper_admin' => __('AdminUser::admin.supperAdmin')] + $this->roleGroups);
                    $form->custom('AdminUser::admin_users.role', [
                        'name' => __('AdminUser::admin.account'),
                        'role_title' => __('AdminUser::admin.roles.user_other_role'),
                        'note_role' => __('AdminUser::admin.roles.user_other_role_note'),
                        'rolePermisions' => $this->rolePermisions,
                        'type' => 'users',
                        'roleName' => 'capabilities'
                    ]);
                $form->endCard();
            $form->endCol();
        $form->endRow();
        $form->action('add');
        // Hiển thị form tại view
        return $form->render('create_multi_col');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \DreamTeam\AdminUser\Http\Requests\AdminUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminUserRequest $requests)
    {
        if ($this->countNumber >= 3 && $this->checkNumber) {
            return redirect()->back()->withErrors('AdminUser::admin.limit_number');
        }
        $redirect = 'edit';
        $status = BaseStatusEnum::DEACTIVE;
        $admin_user_role_id = $is_supper_admin = 0;
        $created_at = $updated_at = date('Y-m-d H:i:s');
        extract($requests->all(), EXTR_OVERWRITE);
        $social['website'] = $website ?? '';
        $social['facebook'] = $facebook ?? '';
        $social['twitter'] = $twitter ?? '';
        $social['pinterest'] = $pinterest ?? '';
        $social['instagram'] = $instagram ?? '';
        $social['youtube'] = $youtube ?? '';
        $social = json_encode($social);
        $capabilities = json_encode($role ?? []);
        if ($redirect == 'save') {
            $status = BaseStatusEnum::DRAFT;
            $redirect = 'edit';
        }
        if ($redirect == 'exit') {
            $redirect = 'index';
        }
        $password = bcrypt($password);
        if ($admin_user_role_id == 'spper_admin') {
            $is_supper_admin = 1;
            $admin_user_role_id = 0;
        } else {
            $is_supper_admin = 0;
            $admin_user_role_id = intval($admin_user_role_id);
        }
        $name = Str::slug($name);
        $name = str_replace('-', '', $name);
        $slug = Str::slug($name);
        $compact = compact('name', 'email', 'phone', 'slug', 'password', 'position', 'display_name', 'avatar', 'summary', 'infomation', 'social', 'admin_user_role_id', 'capabilities', 'status', 'is_supper_admin', 'created_at', 'updated_at');
        DB::beginTransaction();
        try {
            $adminUser = $this->adminUserService->create($compact);
            $this->baseService->handleRelatedRecord($requests, $this->table_name, $adminUser->id, $this->has_seo, $this->has_locale, true, SystemLogStatusEnum::CREATE, $compact, false);
        } catch (\Exception $e) {
            DB::rollback();
            Log::debug('Store ' . $this->table_name . ' error: ' . $e->getMessage());
            return redirect()->back()->withErrors(__('Translate::admin.error_message_catch'));
        }
        DB::commit();
        // Điều hướng
        return redirect(route('admin.' . $this->table_name . '.' . $redirect, $adminUser->id))->with([
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
        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(AdminUser $adminUser)
    {
        $slugItem = $this->slugService->findOne(['table' => 'admin_users', 'table_id' => $adminUser->id]);
        $slug = $slugItem->slug ?? $adminUser->slug ?? '';
        $social = json_decode($adminUser->social ?? '', 1);
        $option = getOption('googleAuthenticate', '', false);
        $isEnabled = $option['enabled'] ?? 0;
        // Khởi tạo form
        $form = new Form;
        $form->row();
            $form->col('col-lg-6');
                $form->card();
                    $form->head(__('AdminUser::admin.story'));
                    $form->row();
                        $form->text('name', $adminUser->name, 1, __('AdminUser::admin.username'), '', false, 'col-lg-6', true);
                        $form->email('email', $adminUser->email, 1, 'Email', '', false, 'col-lg-6', true);
                    $form->endRow();
                    $form->card('toggle-on-off-checkbox');
                        $form->checkbox('change_password', 0, 1, __('AdminUser::admin.forgot_password.change_password'), 'col-lg-6');
                    $form->endCard();
                    $classCheck = 'on-of-checkbox-config hide';
                    $form->card($classCheck);
                        $form->row($classCheck);
                            $form->passwordGenerate('password', '', 0, __('AdminUser::admin.login.password'), '', '', false, 'col-lg-6');
                            $form->password('password_confirm', '', 0, __('AdminUser::admin.forgot_password.password_comfirm'), '', 'password', false, 'col-lg-6');
                        $form->endRow();
                    $form->endCard();
                    $form->head(__('AdminUser::admin.add_info'));
                    $form->image('avatar', $adminUser->avatar, 0, __('Core::admin.general.avatar'), '', '');
                    $form->row();
                        $form->text('display_name', $adminUser->display_name, 0, __('AdminUser::admin.name'), '', false, 'col-lg-6');
                        $form->text('position', $adminUser->position, 0, __('AdminUser::admin.position'), '', false, 'col-lg-6');
                    $form->endRow();
                    $form->text('phone', $adminUser->phone, 1, __('AdminUser::admin.phone'), '', false, 'col-lg-12');
                    $form->textarea('summary', $adminUser->summary, 0, __('AdminUser::admin.summary'), __('AdminUser::admin.summary_placeholder'), false, '', '', false, 200);
                    $form->note(__('AdminUser::admin.summary_note'));
                $form->endCard();
                $form->head(__('AdminUser::admin.link_internet'));
                $form->card();
                    $form->row();
                    $form->text('website', $social['website'] ?? '', 0, 'Website', '', false, 'col-lg-6');
                    $form->text('facebook', $social['facebook'] ?? '', 0, 'Facebook', '', false, 'col-lg-6');
                    $form->endRow();
                    $form->row();
                    $form->text('twitter', $social['twitter'] ?? '', 0, 'Twitter', '', false, 'col-lg-6');
                    $form->text('pinterest', $social['pinterest'] ?? '', 0, 'Pinterest', '', false, 'col-lg-6');
                    $form->endRow();
                    $form->row();
                    $form->text('instagram', $social['instagram'] ?? '', 0, 'Instagram', '', false, 'col-lg-6');
                    $form->text('youtube', $social['youtube'] ?? '', 0, 'Youtube', '', false, 'col-lg-6');
                    $form->endRow();
                    $form->ckeditor('infomation', $adminUser->infomation, 0, __('AdminUser::admin.author_desc'), false, '', '', true);
                    $form->tableOption('FILTER_RENDER_FORM_OPTION_ADMIN_USER', $this->table_name, $adminUser->id);
                    if ($isEnabled) {
                        $google2fa = (new \PragmaRX\Google2FAQRCode\Google2FA());
                        $google2faSecret = $adminUser->google2fa_secret;
                        if (empty($google2faSecret)) $google2faSecret = $google2fa->generateSecretKey();
                        $adminUser->google2fa_secret = $google2faSecret;
                        $adminUser->save();
                        $google2faUrl = $google2fa->getQRCodeInline(
                            getSiteName() . '(' . config('app.url') . ')',
                            $adminUser->email,
                            $google2faSecret
                        );
                        $form->head('AdminUser::admin.2fa');
                        $form->custom('AdminUser::admin_users.custom.qrcode', compact('adminUser', 'google2faUrl', 'google2faSecret'));
                    }
                $form->endCard();
            $form->endCol();
            $form->col('col-lg-6');
                $form->card('', __('AdminUser::admin.role_title'));
                    $form->checkbox('status', $adminUser->status, BaseStatusEnum::ACTIVE, __('Core::admin.general.status'), 'col-lg-12');
                    $form->select('admin_user_role_id', $adminUser->is_supper_admin ? 'spper_admin' : $adminUser->admin_user_role_id, 0, __('AdminUser::admin.roles.select_role'), ['' => __('AdminUser::admin.roles.select_role')] + ['spper_admin' => __('AdminUser::admin.supperAdmin')] + $this->roleGroups);
                    $form->custom('AdminUser::admin_users.role', [
                        'name' => __('AdminUser::admin.account'),
                        'role_title' => __('AdminUser::admin.roles.user_other_role'),
                        'note_role' => __('AdminUser::admin.roles.user_other_role_note'),
                        'rolePermisions' => $this->rolePermisions,
                        'type' => 'users',
                        'roleName' => 'capabilities',
                        'admin_id' => $adminUser->id
                    ]);
                $form->endCard();
            $form->endCol();
        $form->endRow();
        $form->action('edit');

        // Hiển thị form tại view
        return $form->render('edit_multi_col', ['id' => $adminUser->id, 'data_edit' => $adminUser]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \DreamTeam\AdminUser\Http\Requests\AdminUserRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AdminUserRequest $requests, $id)
    {
        $status = BaseStatusEnum::DEACTIVE;
        $admin_user_role_id = $is_supper_admin = 0;
        $enabel_google2fa = 0;
        extract($requests->all(), EXTR_OVERWRITE);
        $social['website'] = $website ?? '';
        $social['facebook'] = $facebook ?? '';
        $social['twitter'] = $twitter ?? '';
        $social['pinterest'] = $pinterest ?? '';
        $social['instagram'] = $instagram ?? '';
        $social['youtube'] = $youtube ?? '';
        $social = json_encode($social);
        $capabilities = json_encode($role ?? []);
        $updated_at = date('Y-m-d H:i:s');
        if ($admin_user_role_id == 'spper_admin') {
            $is_supper_admin = 1;
            $admin_user_role_id = 0;
        } else {
            $is_supper_admin = 0;
            $admin_user_role_id = intval($admin_user_role_id);
        }
        if ($id == Auth::guard('admin')->user()->id) $status = BaseStatusEnum::ACTIVE;
        $compact = compact('admin_user_role_id', 'enabel_google2fa', 'phone', 'position', 'display_name', 'avatar', 'summary', 'infomation', 'social', 'capabilities', 'status', 'is_supper_admin', 'updated_at');
        if (isset($change_password) && isset($password)) {
            $compact['password'] = bcrypt($password);
        }
        DB::beginTransaction();
        try {
            $this->baseService->handleRelatedRecord($requests, $this->table_name, $id, $this->has_seo, $this->has_locale, true, SystemLogStatusEnum::UPDATE, $compact, false);
            $this->adminUserService->update($id, $compact);
        } catch (\Exception $e) {
            DB::rollback();
            Log::debug('Update ' . $this->table_name . ' error: ' . $e->getMessage());
            return redirect()->back()->withErrors(__('Translate::admin.error_message_catch'));
        }
        DB::commit();
        return redirect(route('admin.' . $this->table_name . '.' . $redirect, $id))->with([
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
    public function destroy($id, Request $requests)
    {
        if (!checkRole($this->table_name . '_delete')) {
            return response()->json([
                'status' => 2,
                'message' => __('Core::admin.no_permission')
            ]);
        }
        if ($id == 1 || $id == Auth::guard('admin')->user()->id) {
            return [
                'status' => 2,
                'message' => __('Core::admin.can_delete_users'),
            ];
        } else {
            $checkUserReplace = $this->adminUserService->findOne([
                'id' => $requests->user_id_replace ?? 0
            ]);
            if (!$checkUserReplace) {
                return [
                    'status' => 2,
                    'message' => __('AdminUser::admin.Login.acc_replace'),
                ];
            }
            $checkRecord = $this->adminUserService->read($id);
            DB::beginTransaction();
            try {
                $this->systemLogService->saveLog(SystemLogStatusEnum::DELETE_FOREVER, $checkRecord->toArray(), $this->table_name, $id);
                $this->adminUserService->deleteFromWhereCondition([
                    'id' => $id
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return [
                    'status' => 0,
                    'message' => __('Core::admin.ajax_error')
                ];
            }
            DB::commit();
            return [
                'status' => 1,
                'message' => __('Core::admin.delete_success')
            ];
        }
    }

    /**
     * View đổi mật khẩu
     * @param  requests  $id
     * @return view
     */
    public function changePassword($id)
    {
        // Chỉ tài khoản hiện tại được truy cập và sửa
        if ($id != Auth::guard('admin')->user()->id) {
            return redirect(route('admin.home'))->with([
                'type' => 'success',
                'message' => __('Core::admin.no_permission')
            ]);
        }
        // Khởi tạo form
        $form = new Form;
        $form->passwordGenerate('password', '', 1, 'AdminUser::admin.login.password', 'Core::admin.forgot_password.password_new');
        $form->password('password_confirm', '', 1, 'Core::admin.forgot_password.password_comfirm', 'Core::admin.forgot_password.password_comfirm', 'password');
        $form->action('custom', '', '', '', [
            [
                'type' => 'submit',
                'label' => __('Translate::form.action.save'),
                'btn_type' => 'success',
                'icon' => 'fas fa-save',
                'value' => 'edit',
            ]
        ]);
        // Hiển thị form tại view
        $action = 'change_password';
        $action_name = 'Core::admin.change_password';
        return $form->render('custom', compact('id', 'action', 'action_name'), 'AdminUser::admin_users.change_info');
    }

    /**
     * Xử lý đổi mật khẩu
     * @param  requests  $requests
     * @param  requests  $id
     * @return redirect
     */
    public function changePasswordPost(Request $requests, $id)
    {
        // Chỉ tài khoản hiện tại được truy cập và sửa
        if ($id != Auth::guard('admin')->user()->id) {
            return redirect(route('admin.home'))->with([
                'type' => 'success',
                'message' => __('Core::admin.no_permission')
            ]);
        }
        extract($requests->all(), EXTR_OVERWRITE);
        $password = bcrypt($password);
        $compact = compact('password');
        DB::beginTransaction();
        try {
            $this->adminUserService->update($id, $compact);
        } catch (\Exception $e) {
            DB::rollback();
            Log::debug('Change my password ' . $this->table_name . ' error: ' . $e->getMessage());
            return redirect()->back()->withErrors(__('Translate::admin.error_message_catch'));
        }
        DB::commit();
        return redirect()->back()->with([
            'type' => 'success',
            'message' => __('Core::admin.update_success')
        ]);
    }

    /**
     * View thay đổi thông tin
     * @param  requests  $id
     * @return view
     */
    public function changeInfo($id)
    {
        $adminUser = $this->adminUserService->read($id);
        if ($id != Auth::guard('admin')->user()->id) {
            return redirect(route('admin.home'))->with([
                'type' => 'success',
                'message' => __('Core::admin.no_permission')
            ]);
        }
        $slugItem = $this->slugService->findOne(['table' => 'admin_users', 'table_id' => $adminUser->id]);
        $slug = $slugItem->slug ?? $adminUser->slug ?? '';
        $social = json_decode($adminUser->social ?? '', 1);
        $option = getOption('googleAuthenticate', '', false);
        $isEnabled = $option['enabled'] ?? 0;
        $form = new Form;
        $form->row();
            $form->col('col-lg-6');
                $form->card();
                    $form->head(__('AdminUser::admin.story'));
                    $form->row();
                        $form->text('name', $adminUser->name, 1, __('AdminUser::admin.username'), '', false, 'col-lg-6', true);
                        $form->email('email', $adminUser->email, 1, 'Email', '', false, 'col-lg-6', true);
                    $form->endRow();
                    $form->slug('slug', [
                        'value' => $slug,
                        'dataTable' => $adminUser
                    ], 1, __('Core::admin.general.slug'), '', true, $this->table_name, false);
                    $form->card('toggle-on-off-checkbox');
                        $form->checkbox('change_password', 0, 1, __('AdminUser::admin.forgot_password.change_password'), 'col-lg-6');
                    $form->endCard();
                    $classCheck = 'on-of-checkbox-config hide';
                    $form->card($classCheck);
                        $form->row($classCheck);
                            $form->passwordGenerate('password', '', 0, __('AdminUser::admin.login.password'), '', '', false, 'col-lg-6');
                            $form->password('password_confirm', '', 0, __('AdminUser::admin.forgot_password.password_comfirm'), '', 'password', false, 'col-lg-6');
                        $form->endRow();
                    $form->endCard();
                    $form->head(__('AdminUser::admin.add_info'));
                    $form->image('avatar', $adminUser->avatar, 0, __('Core::admin.general.avatar'), '', '');
                    $form->row();
                        $form->text('display_name', $adminUser->display_name, 0, __('AdminUser::admin.name'), '', false, 'col-lg-6');
                        $form->text('position', $adminUser->position, 0, __('AdminUser::admin.position'), '', false, 'col-lg-6');
                    $form->endRow();
                    $form->text('phone', $adminUser->phone, 1, __('AdminUser::admin.phone'), '', false, 'col-lg-12');
                    $form->textarea('summary', $adminUser->summary, 0, __('AdminUser::admin.summary'), __('AdminUser::admin.summary_placeholder'), false, '', '', false, 200);
                    $form->note(__('AdminUser::admin.summary_note'));
                $form->endCard();
                $form->head(__('AdminUser::admin.link_internet'));
                $form->card();
                    $form->row();
                    $form->text('website', $social['website'] ?? '', 0, 'Website', '', false, 'col-lg-6');
                    $form->text('facebook', $social['facebook'] ?? '', 0, 'Facebook', '', false, 'col-lg-6');
                    $form->endRow();
                    $form->row();
                    $form->text('twitter', $social['twitter'] ?? '', 0, 'Twitter', '', false, 'col-lg-6');
                    $form->text('pinterest', $social['pinterest'] ?? '', 0, 'Pinterest', '', false, 'col-lg-6');
                    $form->endRow();
                    $form->row();
                    $form->text('instagram', $social['instagram'] ?? '', 0, 'Instagram', '', false, 'col-lg-6');
                    $form->text('youtube', $social['youtube'] ?? '', 0, 'Youtube', '', false, 'col-lg-6');
                    $form->endRow();
                    $form->ckeditor('infomation', $adminUser->infomation, 0, __('AdminUser::admin.author_desc'), false, '', '', true);
                    if ($isEnabled) {
                        $google2fa = (new \PragmaRX\Google2FAQRCode\Google2FA());
                        $google2faSecret = $adminUser->google2fa_secret;
                        if (empty($google2faSecret)) $google2faSecret = $google2fa->generateSecretKey();
                        $adminUser->google2fa_secret = $google2faSecret;
                        $adminUser->save();
                        $google2faUrl = $google2fa->getQRCodeInline(
                            getSiteName() . '(' . config('app.url') . ')',
                            $adminUser->email,
                            $google2faSecret
                        );
                        $form->head('AdminUser::admin.2fa');
                        $form->custom('AdminUser::admin_users.custom.qrcode', compact('adminUser', 'google2faUrl', 'google2faSecret'));
                    }
                $form->endCard();
            $form->endCol();
            $form->col('col-lg-6');
                
            $form->endCol();
        $form->endRow();

        $form->action('custom', '', '', '', [
            [
                'type' => 'submit',
                'label' => __('Translate::form.action.save'),
                'btn_type' => 'success',
                'icon' => 'fas fa-save',
                'value' => 'edit',
            ]
        ]);
        // Hiển thị form tại view
        $action = 'change_info';
        $action_name = 'Core::admin.account_info';
        return $form->render('custom', compact('id', 'action', 'action_name', 'adminUser'), 'AdminUser::admin_users.change_info');
    }

    /**
     * Xử lý đổi thông tin
     * @param  requests  $requests
     * @param  requests  $id
     * @return redirect
     */
    public function changeInfoPost(AdminUserRequest $requests, int $id)
    {
        // Chỉ tài khoản hiện tại được truy cập và sửa
        if ($id != Auth::guard('admin')->user()->id) {
            return redirect(route('admin.home'))->with([
                'type' => 'success',
                'message' => __('Core::admin.no_permission')
            ]);
        }
        $enabelGoogle2fa = 0;
        extract($requests->all(), EXTR_OVERWRITE);
        $social['website'] = $website;
        $social['facebook'] = $facebook;
        $social['twitter'] = $twitter;
        $social['pinterest'] = $pinterest;
        $social['instagram'] = $instagram;
        $social['youtube'] = $youtube;
        $social = json_encode($social);
        $created_at = $updated_at = date('Y-m-d H:i:s');
        $enabel_google2fa = intval($requests->get('enabel_google2fa', 0));
        $compact = compact('display_name', 'phone', 'slug', 'position', 'summary', 'avatar', 'infomation', 'social', 'enabel_google2fa', 'updated_at');
        if (isset($change_password) && isset($password)) {
            $compact['password'] = bcrypt($password);
        }
        DB::beginTransaction();
        try {
            $this->baseService->handleRelatedRecord($requests, $this->table_name, $id, $this->has_seo, $this->has_locale, true, SystemLogStatusEnum::UPDATE, $compact, true);
            $this->adminUserService->update($id, $compact);
        } catch (\Exception $e) {
            DB::rollback();
            Log::debug('Change my infor ' . $this->table_name . ' error: ' . $e->getMessage());
            return redirect()->back()->withErrors(__('Translate::admin.error_message_catch'));
        }

        DB::commit();
        return redirect()->back()->with([
            'type' => 'success',
            'message' => __('Core::admin.update_success')
        ]);
    }

    public function faVerify(Request $request):JsonResponse
    {
        $user = Auth::guard('admin')->user();
        $google2fa = (new \PragmaRX\Google2FAQRCode\Google2FA());

        $secret = $request->get('one_time_password', '');
        $valid = $google2fa->verifyKey($user->google2fa_secret, $secret);
        if ($valid) {
            return response()->json([
                'message' => 'Success',
                'type' => 'success'
            ]);
        }
        return response()->json([
            'message' => trans('AdminUser::admin.error_messages.wrong_otp'),
            'type' => 'error'
        ]);
    }
}
