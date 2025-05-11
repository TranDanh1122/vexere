<?php

namespace DreamTeam\Base\Http\Controllers;
use DreamTeam\Base\Http\Controllers\AdminController;

use Illuminate\Http\Request;
use ListData;
use Form;
use DB;
use Illuminate\Http\JsonResponse;
use DreamTeam\Base\Models\Menu;
use DreamTeam\Base\Enums\BaseStatusEnum;
use DreamTeam\Base\Enums\SystemLogStatusEnum;
use DreamTeam\Base\Http\Requests\MenuRequest;
use DreamTeam\Base\Services\Interfaces\MenuServiceInterface;
use DreamTeam\Base\Services\Interfaces\BaseServiceInterface;
use DreamTeam\Base\Services\Interfaces\SystemLogServiceInterface;

class MenuController extends AdminController
{
    protected MenuServiceInterface $menuService;
    protected BaseServiceInterface $baseService;
    protected SystemLogServiceInterface $systemLogService;
    protected array $location;

	function __construct(
        MenuServiceInterface $menuService,
        BaseServiceInterface $baseService,
        SystemLogServiceInterface $systemLogService
    )
    {
        $this->table_name = (new Menu)->getTable();
        $this->module_name = 'Core::admin.menu.title';
        $this->has_seo = false;
        $this->has_locale = true;
        $this->menuService = $menuService;
        $this->baseService = $baseService;
        $this->systemLogService = $systemLogService;
        $this->location = [ 0 => 'Core::admin.menu.select_location' ] + menu_store()->getLocations();
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $requests)
    {
        $locations = menu_store()->getLocations();
        $listdata = new ListData(
                $requests,
                $this->menuService,
                $this->table_name,
                'Core::admin.menu.table',
                [],
                true,
                $this->has_locale,
                30,
                [ $this->table_name.'.id' => 'desc' ]
            );
        // Build Form tìm kiếm
        $listdata->search('name', __('Core::admin.general.name'), 'string');
        $listdata->search('location', 'Core::admin.menu.location_title', 'array', $locations);
        $listdata->search('created_at', __('Core::admin.general.created_at'), 'range');
        $listdata->search('status', __('Core::admin.general.status'), 'array', BaseStatusEnum::tableLabels());
        // Build các hành động
        $listdata->action('status');
        // Build bảng
        $listdata->add('name', __('Core::admin.general.name'), 1);
        $listdata->add('location', __('Core::admin.menu.location_title'), 1);
        $listdata->add('', __('Core::admin.general.time'), 0, 'time');
        $listdata->add('status', __('Core::admin.general.status'), 1, 'status');
        $listdata->add('', 'Language', 0, 'lang');
        $listdata->add('', __('Core::admin.general.action'), 0, 'action');
        return $listdata->render(compact('locations'));
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

        $form->lang($this->table_name);
        $form->text('name', '', 1, __('Core::admin.general.title'), '', false);
        $form->customMenu('value', '', __('Core::admin.menu.list'));
        $form->select('location', '', 0, __('Core::admin.menu.location_title'), $this->location);
        $form->checkbox('status', BaseStatusEnum::ACTIVE, BaseStatusEnum::ACTIVE, __('Core::admin.general.status'));
        $form->action('add');
        // Hiển thị form tại view
        return $form->render('create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \DreamTeam\Base\Http\Requests\MenuRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MenuRequest $requests)
    {
        $status = BaseStatusEnum::DEACTIVE;
        $location = 0;
        extract($requests->all(), EXTR_OVERWRITE);
        if($redirect == 'save'){
            $status = BaseStatusEnum::DRAFT;
            $redirect = 'edit';
        }
        if($redirect == 'exit') {
            $redirect = 'index';
        }
        $location = intval($location);
        $value = base64_encode($value ?? '');
        $created_at = $updated_at = date('Y-m-d H:i:s');
        $compact = compact('name', 'location', 'value', 'status', 'created_at', 'updated_at');
        \DB::beginTransaction();
        try {
            $menu = $this->menuService->create($compact);
            $this->baseService->handleRelatedRecord($requests, $this->table_name, $menu->id, $this->has_seo, $this->has_locale, true, SystemLogStatusEnum::CREATE, $compact, false);
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::debug('Store ' . $this->table_name . ' error: '.$e->getMessage());
            return redirect()->back()->withErrors(__('Translate::admin.error_message_catch'));
        }
        \DB::commit();
        return redirect(route('admin.'.$this->table_name.'.'.$redirect, $menu->id))->with([
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
    public function edit(Menu $menu)
    {
        $id = $menu->id;
        $form = new Form;
        $menu = $menu->load('language_metas');
        $recordLangLocale = $menu->language_metas->lang_locale ?? getLocale();

        $form->lang($this->table_name);
        $form->text('name', $menu->name, 1,  __('Core::admin.general.title'), '', false);
        $form->customMenu('value',  base64_decode($menu->value), __('Core::admin.menu.list'));
        $form->select('location', $menu->location, 0, __('Core::admin.menu.location_title'), $this->location);
        $form->checkbox('status', $menu->status, BaseStatusEnum::ACTIVE,  __('Core::admin.general.status'));
        $form->action('edit');
        // Hiển thị form tại view
        return $form->render('edit', compact('id', 'recordLangLocale'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \DreamTeam\Base\Http\Requests\MenuRequest $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(MenuRequest $requests, $id)
    {
        $status = BaseStatusEnum::DEACTIVE;
        $location = 0;
        extract($requests->all(), EXTR_OVERWRITE);
        $updated_at = date('Y-m-d H:i:s');
        $location = intval($location);
        $value = base64_encode($value ?? '');
        $compact = compact('name', 'location', 'value', 'status', 'updated_at');
        \DB::beginTransaction();
        try {
            $this->baseService->handleRelatedRecord($requests, $this->table_name, $id, $this->has_seo, $this->has_locale, true, SystemLogStatusEnum::UPDATE, $compact, false);
            $this->menuService->update($id, $compact);
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

    public function deleteForever(Request $requests, $id): JsonResponse
    {
        if (!checkRole($this->table_name.'_deleteForever')) {
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
            \Log::error('Delete forever menus error :'. $e->getMessage());
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
        $checkTrashRecord = $this->menuService->findOne([
                'id' => $id,
                'status' => BaseStatusEnum::DELETE
            ], true);
        $data = $checkTrashRecord->toArray();
        $this->systemLogService->saveLog(SystemLogStatusEnum::DELETE_FOREVER, $data, $this->table_name, $id);
        $this->menuService->deleteFromWhereCondition([
                'id' => $id,
                'status' => BaseStatusEnum::DELETE
            ]);
    }
}
