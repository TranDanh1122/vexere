<?php

namespace DreamTeam\Ecommerce\Http\Controllers;

use DreamTeam\Base\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use ListData;
use Form;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use DreamTeam\Ecommerce\Models\Brand;
use DreamTeam\Base\Events\UpdateAttributeImageInContentEvent;
use DreamTeam\Ecommerce\Http\Requests\BrandRequest;
use DreamTeam\Base\Enums\BaseStatusEnum;
use DreamTeam\Base\Enums\SystemLogStatusEnum;
use DreamTeam\Base\Services\Interfaces\BaseServiceInterface;
use DreamTeam\Base\Services\Interfaces\LanguageMetaServiceInterface;
use DreamTeam\Base\Services\Interfaces\SystemLogServiceInterface;
use DreamTeam\Base\Services\Interfaces\SlugServiceInterface;
use DreamTeam\Ecommerce\Services\Interfaces\BrandServiceInterface;
use DreamTeam\Ecommerce\Services\Interfaces\ProductServiceInterface;
use DreamTeam\SyncLink\Services\Interfaces\SyncLinkServiceInterface;

class BrandController extends AdminController
{
    protected BrandServiceInterface $brandService;
    protected ProductServiceInterface $productService;
    protected BaseServiceInterface $baseService;
    protected LanguageMetaServiceInterface $langMetaService;
    protected SystemLogServiceInterface $systemLogService;
    protected SlugServiceInterface $slugService;
    protected SyncLinkServiceInterface $syncLinkService;

    function __construct(
        BrandServiceInterface $brandService,
        ProductServiceInterface $productService,
        BaseServiceInterface $baseService,
        LanguageMetaServiceInterface $langMetaService,
        SystemLogServiceInterface $systemLogService,
        SlugServiceInterface $slugService,
        SyncLinkServiceInterface $syncLinkService
    ) {
        $this->table_name = (new Brand)->getTable();
        $this->module_name = 'Ecommerce::admin.brand';
        $this->has_seo = false;
        $this->has_locale = true;
        $this->brandService = $brandService;
        $this->productService = $productService;
        $this->baseService = $baseService;
        $this->langMetaService = $langMetaService;
        $this->systemLogService = $systemLogService;
        $this->slugService = $slugService;
        $this->syncLinkService = $syncLinkService;

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
            $this->brandService,
            $this->table_name,
            'Ecommerce::brands.table',
            [],
            true,
            $this->has_locale,
            30,
            [$this->table_name . '.id' => 'desc']
        );

        // Build Form tìm kiếm
        $listdata->search('name', __('Biển số'), 'string');
        $listdata->search('status', __('Core::admin.general.status'), 'array', BaseStatusEnum::tableLabels());
        // Build các hành động
        $listdata->action('status');

        // Build bảng
        $listdata->add('name', __('Biển số'), 0);
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
        // Khởi tạo form
        $form = new Form;

        $form->lang($this->table_name, true);
        $form->text('name', '', 1, __('Biển số'), __('Biển số'), true, '', false, 191);
        $form->text('ower_name', '', 1, __('Tên chủ xe'), __('Tên chủ xe'), true, '', false, 191);
        $form->text('ower_phone', '', 1, __('Số điện thoại'), __('Số điện thoại'), true, '', false, 191);
        $form->textarea('address', '', 0, __('Địa chỉ'), __('Địa chỉ'), 5, true, false, false, 500);
        $form->editor('detail', '', 0, __('Ecommerce::admin.content'), true);

        $form->checkbox('status', BaseStatusEnum::ACTIVE, BaseStatusEnum::ACTIVE, __('Ecommerce::admin.status'));
        $form->action('add');
        // Hiển thị form tại view

        return $form->render('create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \DreamTeam\Ecommerce\Http\Requests\BrandRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BrandRequest $requests)
    {
        $status = BaseStatusEnum::DEACTIVE;
        $redirect = 'edit';
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
        $compact = compact('name', 'ower_name', 'ower_phone', 'address', 'detail', 'status', 'created_at', 'updated_at');
        DB::beginTransaction();
        try {
            $brand = $this->brandService->create($compact);
            $this->baseService->handleRelatedRecord($requests, $this->table_name, $brand->id, $this->has_seo, $this->has_locale, true, SystemLogStatusEnum::CREATE, $compact);
        } catch (\Exception $e) {
            DB::rollback();
            Log::debug('Store ' . $this->table_name . ' error: ' . $e->getMessage());
            return redirect()->back()->withErrors(__('Translate::admin.error_message_catch'));
        }
        event(new UpdateAttributeImageInContentEvent($this->table_name, $brand->id, 'detail'));
        DB::commit();
        return redirect(route('admin.' . $this->table_name . '.' . $redirect, $brand->id))->with([
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
    public function edit(Brand $brand)
    {
        $id = $brand->id;
        $brand = $brand->load('language_metas');
        $recordLangLocale = $brand->language_metas->lang_locale ?? getLocale();
        $form = new Form;
        $form->lang($this->table_name);
        $form->text('name', $brand->name, 1, __('Biển số'), __('Biển số'), true, '', false, 191);
        $form->text('ower_name', $brand->ower_name, 1, __('Tên chủ xe'), __('Tên chủ xe'), true, '', false, 191);
        $form->text('ower_phone', $brand->ower_phone, 1, __('Số điện thoại'), __('Số điện thoại'), true, '', false, 191);
        $form->textarea('address', $brand->address, 0, __('Địa chỉ'), __('Địa chỉ'), 5, true, false, false, 500);
        $form->editor('detail', $brand->detail, 0, __('Ecommerce::admin.content'), true);
        $form->checkbox('status', $brand->status, BaseStatusEnum::ACTIVE, __('Ecommerce::admin.status'));
        $form->action('edit');

        $form->action('edit', $brand->getUrl(), $brand->id);
        $data_edit = $brand;

        // Hiển thị form tại view
        return $form->render('edit', compact('id', 'recordLangLocale', 'data_edit'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \DreamTeam\Ecommerce\Http\Requests\BrandRequest  $requests
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BrandRequest $requests, $id)
    {
        $brand = $this->brandService->read($id);
        $status = BaseStatusEnum::DEACTIVE;
        extract($requests->all(), EXTR_OVERWRITE);
        $updated_at = date('Y-m-d H:i:s');
        $compact = compact('name', 'ower_name', 'ower_phone', 'address', 'detail', 'status', 'updated_at');
        DB::beginTransaction();
        try {
            $this->baseService->handleRelatedRecord($requests, $this->table_name, $id, $this->has_seo, $this->has_locale, true, SystemLogStatusEnum::UPDATE, $compact);
            $result = $this->brandService->update($id, $compact);
        } catch (\Exception $e) {
            DB::rollback();
            Log::debug('Update ' . $this->table_name . ' error: ' . $e->getMessage());
            return redirect()->back()->withErrors(__('Translate::admin.error_message_catch'));
        }
        event(new UpdateAttributeImageInContentEvent($this->table_name, $id, 'detail'));
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
        $record = $this->brandService->findOne(compact('id'));
        if (!$record) {
            return response()->json([
                'status' => 0,
                'message' => __('Core::admin.no_data_delete')
            ]);
        }
        DB::beginTransaction();
        try {
            $this->systemLogService->saveLog(SystemLogStatusEnum::QUICK_DELETE, ['status' => BaseStatusEnum::DELETE], $this->table_name, $id);
            $this->brandService->update(
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
        $checkTrashRecord = $this->brandService->findOne([
            'id' => $id,
            'status' => BaseStatusEnum::DELETE
        ], true);
        $data = $checkTrashRecord->toArray();
        $this->systemLogService->saveLog(SystemLogStatusEnum::DELETE_FOREVER, $data, $this->table_name, $id);
        $this->productService->updateFromConditions(['brand_id' => $id], ['brand_id' => 0]);
        $this->brandService->deleteFromWhereCondition([
            'id' => $id,
            'status' => BaseStatusEnum::DELETE
        ]);
    }
}
