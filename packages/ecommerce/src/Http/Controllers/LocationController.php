<?php

namespace DreamTeam\Ecommerce\Http\Controllers;

use DreamTeam\Base\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use ListData;
use Form;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use DreamTeam\Ecommerce\Models\Location;
use DreamTeam\Base\Events\UpdateAttributeImageInContentEvent;
use DreamTeam\Ecommerce\Http\Requests\LocationRequest;
use DreamTeam\Base\Enums\BaseStatusEnum;
use DreamTeam\Base\Enums\SystemLogStatusEnum;
use DreamTeam\Base\Services\Interfaces\BaseServiceInterface;
use DreamTeam\Base\Services\Interfaces\LanguageMetaServiceInterface;
use DreamTeam\Base\Services\Interfaces\SystemLogServiceInterface;
use DreamTeam\Ecommerce\Enums\LocationEnum;
use DreamTeam\Ecommerce\Services\Interfaces\LocationServiceInterface;

class LocationController extends AdminController
{
    protected LocationServiceInterface $locationService;
    protected BaseServiceInterface $baseService;
    protected LanguageMetaServiceInterface $langMetaService;
    protected SystemLogServiceInterface $systemLogService;

    function __construct(
        LocationServiceInterface $locationService,
        BaseServiceInterface $baseService,
        LanguageMetaServiceInterface $langMetaService,
        SystemLogServiceInterface $systemLogService,
    ) {
        $this->table_name = (new Location)->getTable();
        $this->module_name = 'Ecommerce::admin.location';
        $this->has_seo = false;
        $this->has_locale = true;
        $this->locationService = $locationService;
        $this->baseService = $baseService;
        $this->langMetaService = $langMetaService;
        $this->systemLogService = $systemLogService;
        $this->middleware(function ($request, $next) {
            $this->locale = Request()->lang_locale ?? \App::getLocale();
            return $next($request);
        });

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
            $this->locationService,
            $this->table_name,
            'Ecommerce::locations.table',
            [],
            true,
            $this->has_locale,
            30,
            [$this->table_name . '.id' => 'desc']
        );

        // Build Form tìm kiếm
        $listdata->search('name', __('Ecommerce::admin.name_item'), 'string');
        $listdata->search('status', __('Core::admin.general.status'), 'array', BaseStatusEnum::tableLabels());
        // Build các hành động
        $listdata->action('status');

        // Build bảng
        $listdata->add('name', __('Ecommerce::admin.name_item'), 0);
        $listdata->add('name', __('Thuộc địa điểm'), 0);
        $listdata->add('name', __('Thuộc khu vực'), 0);
        $listdata->add('', __('Ecommerce::admin.time'), 0, 'time');
        $listdata->add('status', __('Ecommerce::admin.status'), 0, 'status');
        $listdata->add('', 'Language', 0, 'lang');
        $listdata->add('', __('Core::admin.general.action'), 0, 'action_delete_custom');
        // Lấy dữ liệu data
        $data = $listdata->data();
        // Trả về views
        return $listdata->render(compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $locale = $this->locale;
        // Khởi tạo form
        $form = new Form;

        $form->lang($this->table_name, true);
        $form->text('name', '', 1, __('Tên địa điểm'), __('Tên địa điểm'), true, '', false, 191);
        $form->select('from', '', 1, __('Thuộc khu vực'), ['' => '--- Select ---'] + LocationEnum::labels(),  1, [], true, '');
        $form->select('parent_id', '', 0, __('Thuộc địa điểm'), ['' => '--- Select ---'] + $this->locationService->search()->pluck('name', 'id')->toArray(),  1, [], true, '');
        $form->textarea('address', '', 1, __('Địa chỉ chi tiết'), __('Địa chỉ chi tiết'), 5, true, false);
        $form->textarea('google_map', '' , 0, __('Link google map'), __('Link google map'), 5, true, false);
        
        $form->checkbox('status', BaseStatusEnum::ACTIVE, BaseStatusEnum::ACTIVE, __('Ecommerce::admin.status'));
        $form->action('add');
        // Hiển thị form tại view

        return $form->render('create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \DreamTeam\Ecommerce\Http\Requests\LocationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LocationRequest $requests)
    {
        $status = BaseStatusEnum::DEACTIVE;
        $redirect = 'edit';
        $parent_id = 0;
        extract($requests->all(), EXTR_OVERWRITE);
        $created_at = $created_at ?? date('Y-m-d H:i:s');
        $updated_at = $updated_at ?? date('Y-m-d H:i:s');
        if ($redirect == 'save') {
            $status = BaseStatusEnum::DRAFT;
            $redirect = 'edit';
        }
        if ($redirect == 'exit') {
            $redirect = 'index';
        }
        $parent_id = intval($parent_id);
        $compact = compact('name', 'from', 'address', 'google_map', 'status', 'parent_id', 'created_at', 'updated_at');
        DB::beginTransaction();
        try {
            $location = $this->locationService->create($compact);
            $this->baseService->handleRelatedRecord($requests, $this->table_name, $location->id, $this->has_seo, $this->has_locale, true, SystemLogStatusEnum::CREATE, $compact);
        } catch (\Exception $e) {
            DB::rollback();
            Log::debug('Store ' . $this->table_name . ' error: ' . $e->getMessage());
            return redirect()->back()->withErrors(__('Translate::admin.error_message_catch'));
        }
        DB::commit();
        return redirect(route('admin.' . $this->table_name . '.' . $redirect, $location->id))->with([
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
    public function edit(Location $location)
    {
        $id = $location->id;
        $location = $location->load('language_metas');
        $locale = $this->locale;

        $recordLangLocale = $location->language_metas->lang_locale ?? getLocale();
        $form = new Form;
        $form->text('name', $location->name, 1, __('Tên địa điểm'), __('Tên địa điểm'), true, '', false, 191);
        $form->select('from', $location->from, 1, __('Thuộc khu vực'), ['' => '--- Select ---'] + LocationEnum::labels(),  1, [], true, '');
        $form->select('parent_id', $location->parent_id, 0, __('Thuộc địa điểm'), ['' => '--- Select ---'] + $this->locationService->search()->pluck('name', 'id')->toArray(),  1, [], true, '');
        $form->textarea('address', $location->address, 1, __('Địa chỉ chi tiết'), __('Địa chỉ chi tiết'), 5, true, false);
        $form->textarea('google_map', $location->google_map, 0, __('Link google map'), __('Link google map'), 5, true, false);
        $form->checkbox('status', $location->status, BaseStatusEnum::ACTIVE, __('Ecommerce::admin.status'));
        $form->action('edit');

        // Hiển thị form tại view
        return $form->render('edit', compact('id', 'recordLangLocale'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \DreamTeam\Ecommerce\Http\Requests\LocationRequest  $requests
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(LocationRequest $requests, $id)
    {
        $location = $this->locationService->read($id);
        $status = BaseStatusEnum::DEACTIVE;
        $parent_id = 0;
        extract($requests->all(), EXTR_OVERWRITE);
        $updated_at = date('Y-m-d H:i:s');
        $parent_id = intval($parent_id);
        $compact = compact('name', 'from', 'address', 'google_map', 'status', 'parent_id', 'updated_at');
        DB::beginTransaction();
        try {
            $this->baseService->handleRelatedRecord($requests, $this->table_name, $id, $this->has_seo, $this->has_locale, true, SystemLogStatusEnum::UPDATE, $compact);
            $result = $this->locationService->update($id, $compact);
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
    public function destroy($id)
    {
        if (!checkRole($this->table_name . '_delete')) {
            return response()->json([
                'status' => 2,
                'message' => __('Core::admin.no_permission')
            ]);
        }
        $record = $this->locationService->findOne(compact('id'));
        if (!$record) {
            return response()->json([
                'status' => 0,
                'message' => __('Core::admin.no_data_delete')
            ]);
        }
        DB::beginTransaction();
        try {
            $this->systemLogService->saveLog(SystemLogStatusEnum::QUICK_DELETE, ['status' => BaseStatusEnum::DELETE], $this->table_name, $id);
            $this->locationService->update(
                $id,
                [
                    'status'    => BaseStatusEnum::DELETE
                ]
            );
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Quick Delete ' . $this->table_name . ' error :' . $e->getMessage());
            return response()->json([
                'status' => 0,
                'message' => __('Translate::admin.error_message_catch')
            ]);
        }
        DB::commit();
        return response()->json([
            'status' => 1,
            'message' => __('Translate::admin.delete_success')
        ]);
    }

    public function deleteForever(Request $requests, $id): JsonResponse
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
        $checkTrashRecord = $this->locationService->findOne([
            'id' => $id,
            'status' => BaseStatusEnum::DELETE
        ], true);
        $data = $checkTrashRecord->toArray();
        $this->systemLogService->saveLog(SystemLogStatusEnum::DELETE_FOREVER, $data, $this->table_name, $id);
        DB::table('product_locations')->where('location_id', $id)->delete();
        $this->locationService->deleteFromWhereCondition([
            'id' => $id,
            'status' => BaseStatusEnum::DELETE
        ]);
    }
}
