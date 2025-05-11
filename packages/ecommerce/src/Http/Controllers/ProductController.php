<?php

namespace DreamTeam\Ecommerce\Http\Controllers;

use DreamTeam\Base\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use ListData;
use Form;
use DreamTeam\Ecommerce\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use DreamTeam\Base\Events\UpdateAttributeImageInContentEvent;
use DreamTeam\Ecommerce\Http\Requests\ProductRequest;
use DreamTeam\Base\Enums\BaseStatusEnum;
use DreamTeam\Base\Enums\SystemLogStatusEnum;
use DreamTeam\Base\Services\Interfaces\BaseServiceInterface;
use DreamTeam\Base\Services\Interfaces\LanguageMetaServiceInterface;
use DreamTeam\Base\Services\Interfaces\SystemLogServiceInterface;
use DreamTeam\Base\Services\Interfaces\SlugServiceInterface;
use DreamTeam\Ecommerce\Enums\DirectionTypeEnum;
use DreamTeam\Ecommerce\Enums\LocationEnum;
use DreamTeam\Ecommerce\Enums\LocationTypeEnum;
use DreamTeam\Ecommerce\Services\Interfaces\ProductServiceInterface;
use DreamTeam\SyncLink\Services\Interfaces\SyncLinkServiceInterface;
use DreamTeam\Ecommerce\Services\Interfaces\FilterServiceInterface;
use DreamTeam\Ecommerce\Services\Interfaces\BrandServiceInterface;
use DreamTeam\Ecommerce\Services\Interfaces\LocationServiceInterface;
use DreamTeam\Media\Facades\RvMedia;

class ProductController extends AdminController
{
    protected ProductServiceInterface $productService;
    protected BaseServiceInterface $baseService;
    protected LanguageMetaServiceInterface $langMetaService;
    protected SystemLogServiceInterface $systemLogService;
    protected SlugServiceInterface $slugService;
    protected SyncLinkServiceInterface $syncLinkService;
    protected FilterServiceInterface $filterService;
    protected BrandServiceInterface $brandService;
    protected LocationServiceInterface $locationService;
    protected string $locale;

    function __construct(
        ProductServiceInterface $productService,
        BaseServiceInterface $baseService,
        LanguageMetaServiceInterface $langMetaService,
        SystemLogServiceInterface $systemLogService,
        SlugServiceInterface $slugService,
        SyncLinkServiceInterface $syncLinkService,
        FilterServiceInterface $filterService,
        BrandServiceInterface $brandService,
        LocationServiceInterface $locationService
    ) {
        $this->table_name = (new Product)->getTable();
        $this->module_name = 'Ecommerce::admin.product';
        $this->has_seo = false;
        $this->has_locale = true;
        parent::__construct();
        $this->productService = $productService;
        $this->baseService = $baseService;
        $this->langMetaService = $langMetaService;
        $this->systemLogService = $systemLogService;
        $this->slugService = $slugService;
        $this->syncLinkService = $syncLinkService;
        $this->filterService = $filterService;
        $this->brandService = $brandService;
        $this->locationService = $locationService;
        $this->middleware(function ($request, $next) {
            $this->locale = Request()->lang_locale ?? App::getLocale();
            return $next($request);
        });
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
            $this->productService,
            $this->table_name,
            'Ecommerce::products.table',
            ['brand'],
            true,
            $this->has_locale,
            30,
            [$this->table_name . '.id' => 'desc']
        );
        // Build Form tìm kiếm
        $listdata->search('name', __('Chuyến'), 'string');
        $listdata->search('brand_id', __('Xe'), 'array', $this->brandService->getMultipleWithFromConditions([], ['status' => BaseStatusEnum::ACTIVE], 'id', 'desc', true, $this->locale)
            ->pluck('name', 'id')->toArray());
        $listdata->search('created_at', __('Core::admin.general.created_at'), 'range');
        $listdata->search('status', __('Core::admin.general.status'), 'array', BaseStatusEnum::tableLabels());
        // Build các hành động
        $listdata->action('status');

        // Build bảng
        $listdata->add('name', __('Ecommerce::admin.product_name'), 1);
        $listdata->add('brand_id', __('Hãng xe'), 0);
        $listdata->add('created_at', __('Core::admin.general.time'), 1);
        $listdata->add('', 'Language', 0, 'lang');

        // Trả về views
        return $listdata->render();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $locale = $this->locale;
        if ($request->duplicateID ?? 0) {
            $duplicateRecord = $this->productService->findOne(['id' => $request->duplicateID], false);
        }
        $brands = $this->brandService->getMultipleWithFromConditions([], ['status' => BaseStatusEnum::ACTIVE], 'id', 'desc', true, $locale)
            ->pluck('name', 'id')->toArray();
        $brands = ['' => __('Ecommerce::admin.choose_brand')] + $brands;
        $form = new Form;
        $form->row();
        $form->card('col-lg-8');
        $form->title(__('Ecommerce::admin.information'));
        $form->lang($this->table_name);
        $form->text('name', $duplicateRecord->name ?? '', 1, __('Tên chuyến xe'), __('Tên chuyến xe'), false, '', false, 191);
        $form->select('brand_id', $duplicateRecord->brand_id ?? '', 1, __('Xe chạy chuyến'), ['' => '--- Select ---'] + $brands,  1, [], false, '');
        $form->number('price', $duplicateRecord->price ?? '', 1, __('Giá vé'), '', false, '', false, 1);
        $form->number('price_old', $duplicateRecord->price_old ?? '', 0, __('Giá gốc'), '', false, '', false, 1);
        $form->title(__('Ecommerce::admin.content'));
        // $form->textarea('description', $duplicateRecord->description ?? '', 0, __('Ecommerce::admin.description'), __('Ecommerce::admin.enter_down_line'));
        $form->ckeditor('detail', $duplicateRecord->detail ?? '', 0, 'Chính sách', false, false, '', true);
        [$locationSg, $locationVt] = $this->getLocation();
        $form->title(__('Điểm đón trả chiều Sài Gòn - Vũng Tàu'));
        $productLocations = $duplicateRecord?->productLocations ?? collect();
        $productSchedules = $duplicateRecord?->productSchedules ?? collect();
        $productSchedule = $productSchedules->where('direction', DirectionTypeEnum::SGVT)->first();
        $form->custom('Ecommerce::admin.products.form.schedule', [
            'product_id' => $duplicateRecord->id ?? 0,
            'direction' => DirectionTypeEnum::SGVT,
            'card' => 'directionsgvt',
            'productSchedules' => $productSchedule,
            'time' => $productSchedule->time ?? null,
        ]);
        $form->row();
            $form->col('col-lg-6');
                $form->custom('Ecommerce::admin.products.form.pickup-droff', [
                    'sectionTitle' => 'Điểm đón',
                    'containerId' => 'pickup-sg-vt',
                    'pointsPrefix' => 'pickupsgvt',
                    'locations' => $locationSg,
                    'cardClass' => 'directionsgvt',
                    'points' => $productLocations->where('type', LocationTypeEnum::PICKUP)->where('direction', DirectionTypeEnum::SGVT),
                ]);
            $form->endCol();
            $form->col('col-lg-6');
                $form->custom('Ecommerce::admin.products.form.pickup-droff', [
                    'sectionTitle' => 'Điểm trả',
                    'containerId' => 'droff-sg-vt',
                    'pointsPrefix' => 'droffsgvt',
                    'cardClass' => 'directionsgvt',
                    'locations' => $locationVt,
                    'points' => $productLocations->where('type', LocationTypeEnum::DROPOFF)->where('direction', DirectionTypeEnum::SGVT),
                ]);
            $form->endCol();
        $form->endRow();
        $form->title(__('Điểm đón trả chiều Vũng Tàu - Sài Gòn'));
        $productSchedule = $productSchedules->where('direction', DirectionTypeEnum::VTSG)->first();
        $form->custom('Ecommerce::admin.products.form.schedule', [
            'product_id' => $duplicateRecord->id ?? 0,
            'direction' => DirectionTypeEnum::VTSG,
            'card' => 'directionvtsg',
            'productSchedules' => $productSchedule,
            'time' => $productSchedule->time ?? null,
        ]);
        $form->row();
            $form->col('col-lg-6');
                $form->custom('Ecommerce::admin.products.form.pickup-droff', [
                    'sectionTitle' => 'Điểm đón',
                    'containerId' => 'pickup-vt-sg',
                    'pointsPrefix' => 'pickupvtsg',
                    'locations' => $locationVt,
                    'cardClass' => 'directionvtsg',
                    'points' => $productLocations->where('type', LocationTypeEnum::PICKUP)->where('direction', DirectionTypeEnum::VTSG),
                ]);
            $form->endCol();
            $form->col('col-lg-6');
                $form->custom('Ecommerce::admin.products.form.pickup-droff', [
                    'sectionTitle' => 'Điểm trả',
                    'containerId' => 'droff-vt-sg',
                    'pointsPrefix' => 'droffvtsg',
                    'cardClass' => 'directionvtsg',
                    'locations' => $locationSg,
                    'locationAll' => [
                        'pickupsgvt' => $locationSg,
                        'droffsgvt' => $locationVt,
                        'pickupvtsg' => $locationVt,
                        'droffvtsg' => $locationSg,
                    ],
                    'points' => $productLocations->where('type', LocationTypeEnum::DROPOFF)->where('direction', DirectionTypeEnum::VTSG),
                ]);
            $form->endCol();
        $form->endRow();
        $form->endCard();
        $form->card('col-lg-4');
        $form->image('image', $duplicateRecord->image ?? '', 0, __('Ecommerce::admin.image_thumnail'), __('Ecommerce::admin.choose_image'), __('Core::admin.choose_image_size', ['size' => RvMedia::getSize('products', 'large')]), false, '', ['allow_thumb' => 'yes', 'allow_webp' => 'yes']);
        $form->multiImage('slide', array_filter(explode(',', $duplicateRecord->slide ?? '')) ?? '', 0, __('Ecommerce::admin.image_slide'), null, false, '', ['allow_thumb' => 'yes', 'allow_webp' => 'yes']);
        $form->note(__('Core::admin.choose_image_size', ['size' => RvMedia::getSize('products', 'large')]));
        // Bộ lọc
        $form->custom('Ecommerce::admin.products.form.product_filters', [
            'product_id' => $duplicateRecord->id ?? 0,
        ]);
        $form->checkbox('status', $duplicateRecord->status ?? 1, 1, 'Ecommerce::admin.status', 'col-lg-4');
        $form->endCard();
        $form->endRow();
        $form->tableOption('FILTER_RENDER_FORM_OPTION_PRODUCT', $this->table_name);

        $form->action('add');
        return $form->render('create_multi_col');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \DreamTeam\Ecommerce\Http\Requests\ProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $requests)
    {
        $status = BaseStatusEnum::DEACTIVE;
        $redirect = 'edit';
        $image = $description = $detail = '';
        extract($requests->all(), EXTR_OVERWRITE);
        if (isset($slide) && is_array($slide) && count($slide)) {
            $slide = implode(',', array_filter($slide));
        } else {
            $slide = null;
        }
        $created_at = $updated_at = date('Y-m-d H:i:s');
        if ($redirect == 'save') {
            $status = BaseStatusEnum::DRAFT;
            $redirect = 'edit';
        }
        if ($redirect == 'exit') {
            $redirect = 'index';
        }
        $price = intval(str_replace(',', '', $price ?? 0));
        $price_old = intval(str_replace(',', '', $price_old ?? 0));
        $compact = compact('brand_id', 'name', 'image', 'slide', 'description', 'detail', 'price', 'price_old', 'status', 'created_at', 'updated_at');

        DB::beginTransaction();
        try {
            $product = $this->productService->create($compact);
            $this->baseService->handleRelatedRecord($requests, $this->table_name, $product->id, $this->has_seo, $this->has_locale, true, SystemLogStatusEnum::CREATE, $compact);
            $this->filterService->setProductFilter($filters ?? [], $product->id);
            $times = $this->locationService->saveProductLocations($product->id, $requests->all());
            $this->productService->update($product->id, [
                'start_time_sg_vt' => $times['start_time_sg_vt'] ?? null,
                'start_time_vt_sg' => $times['start_time_vt_sg'] ?? null,
            ]);
            $this->productService->saveProductSchedule($product->id, $requests->all());
        } catch (\Exception $e) {
            DB::rollback();
            Log::debug('Store ' . $this->table_name . ' error: ' . $e->getMessage());
            return redirect()->back()->withErrors(__('Translate::admin.error_message_catch'));
        }
        event(new UpdateAttributeImageInContentEvent($this->table_name, $product->id, 'detail'));
        DB::commit();
        return redirect(route('admin.' . $this->table_name . '.' . $redirect, $product->id))->with([
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
    public function edit(Product $product)
    {
        $id = $product->id;
        $product = $product->load('language_metas', 'productLocations');
        $languageMeta = $product->language_metas;
        $recordLangLocale = $languageMeta->lang_locale ?? $this->locale;
        $brands = $this->brandService->getMultipleWithFromConditions([], ['status' => BaseStatusEnum::ACTIVE], 'id', 'desc', true, $recordLangLocale)
            ->pluck('name', 'id')->toArray();
        $brands = ['' => __('Ecommerce::admin.choose_brand')] + $brands;
        $form = new Form;
        $form->row();
        $form->card('col-lg-8');
        $form->title(__('Ecommerce::admin.information'));
        $form->lang($this->table_name);
        $form->text('name', $product->name ?? '', 1, __('Tên chuyến xe'), __('Tên chuyến xe'), false, '', false, 191);
        $form->select('brand_id', $product->brand_id ?? '', 1, __('Xe chạy chuyến'), ['' => '--- Select ---'] + $brands,  1, [], false, '');
        $form->number('price', $product->price ?? '', 1, __('Giá vé'), '', false, '', false, 1);
        $form->number('price_old', $product->price_old ?? '', 0, __('Giá gốc'), '', false, '', false, 1);
        $form->title(__('Ecommerce::admin.content'));
        // $form->textarea('description', $product->description ?? '', 0, __('Ecommerce::admin.description'), __('Ecommerce::admin.enter_down_line'));
        $form->ckeditor('detail', $product->detail ?? '', 0, 'Chính sách', false, false, '', true);
        [$locationSg, $locationVt] = $this->getLocation();
        $form->title(__('Điểm đón trả chiều Sài Gòn - Vũng Tàu'));
        $productSchedules = $product?->productSchedules ?? collect();
        $productSchedule = $productSchedules->where('direction', DirectionTypeEnum::SGVT)->first();
        $form->custom('Ecommerce::admin.products.form.schedule', [
            'product_id' => $product->id,
            'direction' => DirectionTypeEnum::SGVT,
            'card' => 'directionsgvt',
            'productSchedules' => $productSchedule,
            'time' => $productSchedule->time ?? null,
        ]);
        $productLocations = $product->productLocations;
        $form->row();
            $form->col('col-lg-6');
                $form->custom('Ecommerce::admin.products.form.pickup-droff', [
                    'sectionTitle' => 'Điểm đón',
                    'containerId' => 'pickup-sg-vt',
                    'pointsPrefix' => 'pickupsgvt',
                    'locations' => $locationSg,
                    'cardClass' => 'directionsgvt',
                    'points' => $productLocations->where('type', LocationTypeEnum::PICKUP)->where('direction', DirectionTypeEnum::SGVT),
                ]);
            $form->endCol();
            $form->col('col-lg-6');
                $form->custom('Ecommerce::admin.products.form.pickup-droff', [
                    'sectionTitle' => 'Điểm trả',
                    'containerId' => 'droff-sg-vt',
                    'pointsPrefix' => 'droffsgvt',
                    'cardClass' => 'directionsgvt',
                    'locations' => $locationVt,
                    'points' => $productLocations->where('type', LocationTypeEnum::DROPOFF)->where('direction', DirectionTypeEnum::SGVT),
                ]);
            $form->endCol();
        $form->endRow();
        $form->title(__('Điểm đón trả chiều Vũng Tàu - Sài Gòn'));
        $productSchedule = $productSchedules->where('direction', DirectionTypeEnum::VTSG)->first();
        $form->custom('Ecommerce::admin.products.form.schedule', [
            'product_id' => $product->id,
            'direction' => DirectionTypeEnum::VTSG,
            'card' => 'directionvtsg',
            'productSchedules' => $productSchedule,
            'time' => $productSchedule->time ?? null,
        ]);
        $form->row();
            $form->col('col-lg-6');
                $form->custom('Ecommerce::admin.products.form.pickup-droff', [
                    'sectionTitle' => 'Điểm đón',
                    'containerId' => 'pickup-vt-sg',
                    'pointsPrefix' => 'pickupvtsg',
                    'locations' => $locationVt,
                    'cardClass' => 'directionvtsg',
                    'points' => $productLocations->where('type', LocationTypeEnum::PICKUP)->where('direction', DirectionTypeEnum::VTSG),
                ]);
            $form->endCol();
            $form->col('col-lg-6');
                $form->custom('Ecommerce::admin.products.form.pickup-droff', [
                    'sectionTitle' => 'Điểm trả',
                    'containerId' => 'droff-vt-sg',
                    'pointsPrefix' => 'droffvtsg',
                    'cardClass' => 'directionvtsg',
                    'locations' => $locationSg,
                    'locationAll' => [
                        'pickupsgvt' => $locationSg,
                        'droffsgvt' => $locationVt,
                        'pickupvtsg' => $locationVt,
                        'droffvtsg' => $locationSg,
                    ],
                    'points' => $productLocations->where('type', LocationTypeEnum::DROPOFF)->where('direction', DirectionTypeEnum::VTSG),
                ]);
            $form->endCol();
        $form->endRow();
        $form->endCard();
        $form->card('col-lg-4');
        $form->image('image', $product->image ?? '', 0, __('Ecommerce::admin.image_thumnail'), __('Ecommerce::admin.choose_image'), __('Core::admin.choose_image_size', ['size' => RvMedia::getSize('products', 'large')]), false, '', ['allow_thumb' => 'yes', 'allow_webp' => 'yes']);
        $form->multiImage('slide', array_filter(explode(',', $product->slide ?? '')) ?? '', 0, __('Ecommerce::admin.image_slide'), null, false, '', ['allow_thumb' => 'yes', 'allow_webp' => 'yes']);
        $form->note(__('Core::admin.choose_image_size', ['size' => RvMedia::getSize('products', 'large')]));
        // Bộ lọc
        $form->custom('Ecommerce::admin.products.form.product_filters', [
            'product_id' => $product->id ?? 0,
        ]);
        $form->checkbox('status', $product->status ?? 1, 1, 'Ecommerce::admin.status', 'col-lg-4');
        $form->endCard();
        $form->endRow();
        $form->tableOption('FILTER_RENDER_FORM_OPTION_PRODUCT', $this->table_name);

        $form->action('edit');
        $data_edit = $product;
        return $form->render('edit_multi_col', compact('id', 'data_edit', 'recordLangLocale'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \DreamTeam\Ecommerce\Http\Requests\ProductRequest  $requests
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $requests, $id)
    {
        $product = $this->productService->read($id);
        $status = BaseStatusEnum::DEACTIVE;
        $image = $description = $detail = '';
        extract($requests->all(), EXTR_OVERWRITE);
        if (isset($slide) && is_array($slide) && count($slide)) {
            $slide = implode(',', array_filter($slide));
        } else {
            $slide = null;
        }
        $price = intval(str_replace(',', '', $price ?? 0));
        $price_old = intval(str_replace(',', '', $price_old ?? 0));
        $updated_at = date('Y-m-d H:i:s');
        $compact = compact('brand_id', 'name', 'image', 'slide', 'description', 'detail', 'price', 'price_old', 'status', 'updated_at');

        DB::beginTransaction();
        try {
            $this->baseService->handleRelatedRecord($requests, $this->table_name, $id, $this->has_seo, $this->has_locale, true, SystemLogStatusEnum::UPDATE, $compact);
            $result = $this->productService->update($id, $compact);
            $this->filterService->setProductFilter($filters ?? [], $product->id);
            $times = $this->locationService->saveProductLocations($product->id, $requests->all());
            $this->productService->update($product->id, [
                'start_time_sg_vt' => $times['start_time_sg_vt'] ?? null,
                'start_time_vt_sg' => $times['start_time_vt_sg'] ?? null,
            ]);
            $this->productService->saveProductSchedule($id, $requests->all());
        } catch (\Exception $e) {
            DB::rollback();
            Log::debug('Update ' . $this->table_name . ' error: ' . $e->getMessage());
            Log::error($e);
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
        //
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
            Log::error('Delete forever products error :' . $e->getMessage());
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
        $productLocation = $this->locationService->getProductLocationsByProduct($id);
        $productLocation = $productLocation ? $productLocation->toArray() : [];
        $this->productService->deleteForever($id, $productLocation);
        $this->locationService->deleteProductLocationByProduct($id);
    }

    /**
     * Load thông số bộ lọc
     */
    public function getFilter(Request $requests)
    {
        $productId = $requests->product_id ?? 0;
        $response = $this->filterService->getProductCategoryFilterMapCruds(0, $productId);
        $compact = array_merge($response, compact('productId'));
        $html = view('Ecommerce::admin.products.form.product_filter_item', $compact)->render();
        return [
            'status' => 1,
            'html' => $html
        ];
    }

    private function getLocation(): array
    {
        $locationSg = $this->locationService->getMultipleWithFromConditions([], [
            'status' => BaseStatusEnum::ACTIVE,
            'from' => LocationEnum::SG,
        ], 'id', 'desc');
        $locationVt = $this->locationService->getMultipleWithFromConditions([], [
            'status' => BaseStatusEnum::ACTIVE,
            'from' => LocationEnum::VT,
        ], 'id', 'desc');
        // xử lý đưa về mảng theo cấp độ, chỉ có 2 cấp, phân cấp dụa theo parent_id
        // vd: [1 => 'Tên', 2 => '- Tên là con của 2']
        $formattedLocationSg = [];
        $formattedLocationVt = [];
        
        // Process Saigon locations
        foreach ($locationSg->where('parent_id', 0) as $location) {
            $formattedLocationSg[] = ['id' => $location->id, 'name' => $location->name];
            foreach ($locationSg->where('parent_id', $location->id) as $childLocation) {
                $formattedLocationSg[] = ['id' => $childLocation->id, 'name' => '- ' . $childLocation->name];
            }
        }
        
        // Process Vung Tau locations
        foreach ($locationVt->where('parent_id', 0) as $location) {
            $formattedLocationVt[] = ['id' => $location->id, 'name' => $location->name];
            foreach ($locationVt->where('parent_id', $location->id) as $childLocation) {
                $formattedLocationVt[] = ['id' => $childLocation->id, 'name' => '- ' . $childLocation->name];
            }
        }
        return [$formattedLocationSg, $formattedLocationVt];
    }

}
