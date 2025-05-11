<?php

namespace DreamTeam\Ecommerce\Http\Controllers;

use DreamTeam\Base\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use ListData;
use Form;
use DB;
use Illuminate\Http\JsonResponse;
use DreamTeam\Ecommerce\Models\Filter;
use DreamTeam\Base\Events\UpdateAttributeImageInContentEvent;
use DreamTeam\Ecommerce\Http\Requests\FilterRequest;
use DreamTeam\Base\Enums\BaseStatusEnum;
use DreamTeam\Base\Enums\SystemLogStatusEnum;
use DreamTeam\Base\Services\Interfaces\BaseServiceInterface;
use DreamTeam\Base\Services\Interfaces\LanguageMetaServiceInterface;
use DreamTeam\Base\Services\Interfaces\SystemLogServiceInterface;
use DreamTeam\Ecommerce\Services\Interfaces\FilterServiceInterface;
use DreamTeam\Ecommerce\Services\Interfaces\ProductServiceInterface;

class FilterController extends AdminController
{

	protected FilterServiceInterface $filterService;
    protected ProductServiceInterface $productService;
    protected BaseServiceInterface $baseService;
    protected LanguageMetaServiceInterface $langMetaService;
    protected SystemLogServiceInterface $systemLogService;

    function __construct(
        FilterServiceInterface $filterService,
        ProductServiceInterface $productService,
        BaseServiceInterface $baseService,
        LanguageMetaServiceInterface $langMetaService,
        SystemLogServiceInterface $systemLogService
    )
    {
        $this->table_name = (new Filter)->getTable();
        $this->module_name = 'Ecommerce::admin.list_filter';
        $this->has_seo = false;
        $this->has_locale = true;
        $this->filterService = $filterService;
        $this->productService = $productService;
        $this->baseService = $baseService;
        $this->langMetaService = $langMetaService;
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
                $this->filterService,
                $this->table_name,
                'Ecommerce::filters.table',
                [],
                true,
                $this->has_locale,
                30,
                [ $this->table_name . '.order' => 'asc', $this->table_name . '.id' => 'desc' ]
            );
        // Build Form tìm kiếm
        $listdata->search('name', __('Ecommerce::admin.name_item'), 'string');
        $listdata->search('created_at', __('Ecommerce::admin.created_at'), 'range');
        $listdata->search('status', __('Core::admin.general.status'), 'array', BaseStatusEnum::tableLabels());
        // Build các hành động
        $listdata->action('status');
        // Build bảng
        $listdata->add('name', __('Ecommerce::admin.name_item'), 1);
        $listdata->add('order', __('Ecommerce::admin.sort'), 1, 'order');
        $listdata->add('', __('Ecommerce::admin.time'), 0, 'time');
        $listdata->add('status', __('Ecommerce::admin.status'), 1, 'status');
        $listdata->add('', 'Language', 0, 'lang');
       $listdata->add('', __('Core::admin.general.action'), 0, 'action_delete_custom');
        // Trả về views
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

        $form->card('col-lg-3', __('Ecommerce::admin.infor_list_filter'));
            $form->lang($this->table_name);
            $form->text('name', '', 1, __('Ecommerce::admin.title'));
            $form->checkbox('status', BaseStatusEnum::ACTIVE, BaseStatusEnum::ACTIVE, __('Ecommerce::admin.status'));
            $form->custom('Form::custom.form_custom', [
                'has_full' => true,
                'name' => 'filter_details',
                'value' => [],
                'label' => __('Ecommerce::admin.add_filter'),
                'generate' => [
                    [ 'type' => 'textarea', 'name' => 'name', 'placeholder' => __('Ecommerce::admin.enter_filter_name'), ],
                ],
            ]);
        $form->endCard();

        $form->custom('Ecommerce::filters.filter_details', [
            'filter_id' => 0,
        ]);

        $form->action('add');
        // Hiển thị form tại view
        return $form->render('create_multi_col');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \DreamTeam\Ecommerce\Http\Requests\FilterRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FilterRequest $requests)
    {
        $status = BaseStatusEnum::DEACTIVE;
        $redirect = 'edit';
        extract($requests->all(), EXTR_OVERWRITE);
        $created_at = $updated_at = date('Y-m-d H:i:s');
        if($redirect == 'save'){
            $status = BaseStatusEnum::DRAFT;
            $redirect = 'edit';
        }
        if($redirect == 'exit') {
            $redirect = 'index';
        }
        $name_web = $name_web ?? $name;
        $compact = compact('name', 'status', 'created_at', 'updated_at');
        \DB::beginTransaction();
        try {
            $filter = $this->filterService->create($compact);
            $this->baseService->handleRelatedRecord($requests, $this->table_name, $filter->id, $this->has_seo, $this->has_locale, true, SystemLogStatusEnum::CREATE, $compact, false);
            $this->filterService->storeFilterDetail($filter_details['name'] ?? [], $filter->id);
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::debug('Store '. $this->table_name .' error: '.$e->getMessage());
            return redirect()->back()->withErrors(__('Translate::admin.error_message_catch'));
        }
        \DB::commit();
        return redirect(route('admin.'.$this->table_name.'.'.$redirect, $filter->id))->with([
            'type' => 'success',
            'message' => __('Translate::admin.create_success').' '.__('Ecommerce::admin.choose_filter_category')
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
    public function edit(Filter $filter)
    {
        $id = $filter->id;
        $filter = $filter->load('language_metas');
        $recordLangLocale = $filter->language_metas->lang_locale ?? getLocale();
        $conditions = [
            'filter_id' => ['=' => $id]
        ];
        if(Request()->trash) {
            $conditions['status'] = ['=' => BaseStatusEnum::DELETE];
        } else {
            $conditions['status'] = ['<>' => BaseStatusEnum::DELETE];
        }
        $filterDetails = $this->filterService->getFilterDetailByConditions($conditions);
        $statusEnum = \DreamTeam\Base\Enums\BaseStatusEnum::labels();
        unset($statusEnum[BaseStatusEnum::DRAFT]);
        unset($statusEnum[BaseStatusEnum::DELETE_FOREVER]);
        $form = new Form;
        $form->card('col-lg-3', __('Ecommerce::admin.infor_list_filter'));
            $form->text('filter_name', $filter->name, 1, __('Ecommerce::admin.title'));
            $form->checkbox('filter_status', $filter->status, BaseStatusEnum::ACTIVE, __('Ecommerce::admin.status'));
            $form->custom('Form::custom.form_custom', [
                'has_full' => true,
                'name' => 'filter_details',
                'value' => [],
                'label' => __('Ecommerce::admin.add_filter'),
                'generate' => [
                    [ 'type' => 'textarea', 'name' => 'name', 'placeholder' => __('Ecommerce::admin.enter_filter_name'), ],
                ],
            ]);
        $form->endCard();

        $form->custom('Ecommerce::filters.filter_details', [
            'filter_id' => $id,
            'filterDetails' => $filterDetails,
            'statusEnum' => $statusEnum,
        ]);

        $form->action('edit');
        // Hiển thị form tại view
        return $form->render('edit_multi_col', compact('id', 'recordLangLocale'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \DreamTeam\Ecommerce\Http\Requests\FilterRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(FilterRequest $requests, $id)
    {
    	$this->filterService->read($id);
        $status = BaseStatusEnum::DEACTIVE;
        extract($requests->all(), EXTR_OVERWRITE);
        $name = $filter_name ?? null;
        $status = $filter_status ?? $status;
        $updated_at = date('Y-m-d H:i:s');
        $compact = compact('name', 'status', 'updated_at');
        \DB::beginTransaction();
        try {
            $this->baseService->handleRelatedRecord($requests, $this->table_name, $id, $this->has_seo, $this->has_locale, true, SystemLogStatusEnum::UPDATE, $compact, false);
            $this->filterService->update($id, $compact);
            $this->filterService->storeFilterDetail($filter_details['name'] ?? [], $id);
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::debug('Update '. $this->table_name .' error: '.$e->getMessage());
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
        if (!checkRole($this->table_name.'_delete')) {
            return response()->json([
                'status' => 2,
                'message' => __('Core::admin.no_permission')
            ]);
        }
        $record = $this->filterService->findOne(compact('id'));
        if (!$record) {
            return response()->json([
                'status' => 0,
                'message' => __('Core::admin.no_data_delete')
            ]);
        }
        \DB::beginTransaction();
        try {
            $this->systemLogService->saveLog(SystemLogStatusEnum::QUICK_DELETE, ['status' => BaseStatusEnum::DELETE], $this->table_name, $id);
            $this->filterService->update(
                    $id,
                    [
                        'status'    => BaseStatusEnum::DELETE
                    ]
                );
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error('Quick Delete '. $this->table_name .' error :'. $e->getMessage());
            return response()->json([
                'status' => 0,
                'message' => __('Translate::admin.error_message_catch')
            ]);
        }
        \DB::commit();
        return response()->json([
            'status' => 1,
            'message' => __('Translate::admin.delete_success')
        ]);
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
            \Log::error('Delete forever '. $this->table_name .' error :'. $e->getMessage());
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
        $checkTrashRecord = $this->filterService->findOne([
                'id' => $id,
                'status' => BaseStatusEnum::DELETE
            ], true);
        $data = $checkTrashRecord->toArray();
        $filterDetails = $this->filterService->getFilterDetailByConditions(['filter_id' => ['=' => $id]]);
        $filterDetailIDS = $filterDetails->pluck('id')->toArray();
        $productFilters = $this->filterService->getProductFilterInDetail($filterDetailIDS)->toArray();
        $filterProductCategoryMaps = $this->filterService->getFilterMapCategoryByConditions(['filter_id' => $id])->toArray();
        $data['filterDetails'] = $filterDetails->toArray();
        $data['productFilters'] = $productFilters;
        $data['filterProductCategoryMaps'] = $filterProductCategoryMaps;
        $this->systemLogService->saveLog(SystemLogStatusEnum::DELETE_FOREVER, $data, $this->table_name, $id);
        $this->filterService->deleteFilterDetailByConditions(['filter_id' => $id]);
        $this->filterService->deleteProductFilterInDetail($filterDetailIDS);
        $this->filterService->deleteFilterMapCategoryByConditions(['filter_id' => $id]);
        $this->filterService->deleteFromWhereCondition([
                'id' => $id,
                'status' => BaseStatusEnum::DELETE
            ]);
    }
    /**
     * Delete forever filter datail
     */
    public function deleteForeverFilterDetail(Request $requests, $id): JsonResponse
    {
        if (!checkRole('filter_details_delete')) {
            return response()->json([
                'status' => 2,
                'message' => __('Core::admin.no_permission')
            ]);
        }
        $checkTrashRecord = $this->filterService->getFilterDetailByConditions([
                'id' => ['=' => $id],
                'status' => ['=' => BaseStatusEnum::DELETE]
            ])->first();
        if (!$checkTrashRecord) {
            return response()->json([
                'status' => 2,
                'message' => __('Core::admin.no_data_delete')
            ]);
        }
        \DB::beginTransaction();
        try {
            $data = $checkTrashRecord->toArray();
            $productFilters = $this->filterService->getProductFilterByConditions(['filter_detail_id' => $checkTrashRecord->id])->toArray();
            $data['productFilters'] = $productFilters;
            $this->systemLogService->saveLog(SystemLogStatusEnum::DELETE_FOREVER, $data, 'filter_details', $id);
            $this->filterService->deleteProductFilterByConditions(['filter_detail_id' => $checkTrashRecord->id]);
            $this->filterService->deleteFilterDetailByConditions([
                'id' => $id,
                'status' => BaseStatusEnum::DELETE
            ]);
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error('Delete forever filter_details error :'. $e->getMessage());
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

}
