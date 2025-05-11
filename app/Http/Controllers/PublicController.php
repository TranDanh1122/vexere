<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use DreamTeam\Base\Enums\BaseStatusEnum;
use DreamTeam\Base\Events\ClearCacheEvent;
use DreamTeam\Ecommerce\Enums\DirectionTypeEnum;
use DreamTeam\Ecommerce\Enums\LocationEnum;
use DreamTeam\Ecommerce\Services\Interfaces\BrandServiceInterface;
use DreamTeam\Ecommerce\Services\Interfaces\FilterServiceInterface;
use DreamTeam\Ecommerce\Services\Interfaces\LocationServiceInterface;
use DreamTeam\Ecommerce\Services\Interfaces\OrderServiceInterface;
use DreamTeam\Ecommerce\Services\Interfaces\ProductServiceInterface;
use Illuminate\Support\Collection;
use Barryvdh\DomPDF\Facade\Pdf;

class PublicController extends Controller
{
    protected ProductServiceInterface $productService;
    protected BrandServiceInterface $brandService;
    protected FilterServiceInterface $filterService;
    protected OrderServiceInterface $orderService;
    public function __construct(
        ProductServiceInterface $productService,
        BrandServiceInterface $brandService,
        FilterServiceInterface $filterService,
        OrderServiceInterface $orderService
    ) {
        $this->productService = $productService;
        $this->brandService = $brandService;
        $this->filterService = $filterService;
        $this->orderService = $orderService;
        parent::__construct();
    }

    public function findSlots(Request $request)
    {
        $data = $request->all();
        $from = $data['departure'] ?? null;
        $to = $data['destination'] ?? null;
        $fromDate = $data['departureDate'] ?? null;
        $toDate = $data['returnDate'] ?? null;

        if ($from === 'saigon') {
            $route = 'app.getslotsgvt.vi';
        } else {
            $route = 'app.getslotvtsg.vi';
        }
        $url = route($route, [
            'from' => $from,
            'to' => $to,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
        ]);
        return response()->json([
            'error' => false,
            'redirectUrl' => $url,
        ]);
    }

    public function getSlotVtSg(Request $request)
    {
        [
            $fromDate,
            $toDate,
            $realDayFromDate,
            $realDayToDate,
            $fromCurrentDay,
            $toCurrentDay,
            $dataRequest,
            $brands,
            $filters,
            $selectedBrands,
            $selectedFilterDetails,
            $filterDetailIds,
            $times,
            $prices,
            $locationStarts,
            $locationReturns,
            $locationCheckedStarts,
            $locationCheckedReturns
        ] = $this->prepareData($request);
        // dd($fromCurrentDay, $toCurrentDay, $realDayFromDate, $realDayToDate, $fromDate, $toDate);
        // lẤY CAC XE TINH TRANG CHAY TU SAIGON DEN VUNG TAU, CÓ LỊCH CHẠY NGÀY $realDayFromDate = 1
        $products = $this->productService->getProductByConditions([
            'direction' => DirectionTypeEnum::VTSG,
            'directionReturn' => DirectionTypeEnum::SGVT,
            'day' => $realDayFromDate,
            'date' => $fromDate,
            'isCurrentDay' => $fromCurrentDay,
            'directionTime' => 'start_time_vt_sg',
            'brands' => $selectedBrands,
            'fillters' => $filterDetailIds,
            'times' => $times,
            'prices' => $prices,
            'locationCheckedStarts' => $locationCheckedStarts,
            'locationCheckedReturns' => $locationCheckedReturns
        ]);
        $returnProducts = collect();
        if ($realDayToDate && $toDate) {
            // lẤY CAC XE TINH TRANG CHAY TU  VUNG TAU DEN SAIGON, CÓ LỊCH CHẠY NGÀY $realDayToDate = 1
            $returnProducts = $this->productService->getProductByConditions([
                'direction' => DirectionTypeEnum::SGVT,
                'directionReturn' => DirectionTypeEnum::VTSG,
                'day' => $realDayToDate,
                'date' => $toDate,
                'isCurrentDay' => $toCurrentDay,
                'directionTime' => 'start_time_sg_vt',
                'brands' => $selectedBrands,
                'fillters' => $filterDetailIds,
                'times' => $times,
                'prices' => $prices,
                'locationCheckedStarts' => $locationCheckedStarts,
                'locationCheckedReturns' => $locationCheckedReturns
            ]);
        }
        $type = $request->type ?? 'replace';
        $settingGeneral = getOption('general', getLocale());
        $meta_seo = metaSeo('', '', [
            'title' => $settingGeneral['vt_sg_title'] ?? __('Đặt vé xe Vũng Tàu - Sài Gòn'),
            'description' => $settingGeneral['vt_sg_description'] ?? __('Đặt vé xe Vũng Tàu - Sài Gòn'),
            'social_title' => $settingGeneral['vt_sg_title'] ?? __('Đặt vé xe Vũng Tàu - Sài Gòn'),
            'social_description' => $settingGeneral['vt_sg_description'] ?? __('Đặt vé xe Vũng Tàu - Sài Gòn'),
        ]);
        $isShowReturn = $request->get('isShowReturn', 0);
        if ($isShowReturn == 1) {
            $products = $returnProducts;
        }
        $compact = compact(
            'meta_seo',
            'products',
            'returnProducts',
            'fromDate',
            'toDate',
            'realDayFromDate',
            'realDayToDate',
            'dataRequest',
            'brands',
            'filters',
            'type',
            'locationStarts',
            'locationReturns',
            'selectedBrands',
            'selectedFilterDetails',
            'filterDetailIds',
            'times',
            'prices',
            'locationCheckedStarts',
            'locationCheckedReturns',
            'isShowReturn'
        );
        if ($request->ajax()) {
            $html = view('search.result', $compact)->render();
            $topHtml = view('search.result-top', $compact)->render();
            return response()->json([
                'error' => false,
                'html' => $html,
                'topHtml' => $topHtml,
                'type' => $type
            ]);
        }
        return view('search.index', $compact);
    }

    public function getSlotSgVt(Request $request)
    {
        [
            $fromDate,
            $toDate,
            $realDayFromDate,
            $realDayToDate,
            $fromCurrentDay,
            $toCurrentDay,
            $dataRequest,
            $brands,
            $filters,
            $selectedBrands,
            $selectedFilterDetails,
            $filterDetailIds,
            $times,
            $prices,
            $locationStarts,
            $locationReturns,
            $locationCheckedStarts,
            $locationCheckedReturns
        ] = $this->prepareData($request);
        // dd($fromCurrentDay, $toCurrentDay, $realDayFromDate, $realDayToDate, $fromDate, $toDate);
        // lẤY CAC XE TINH TRANG CHAY TU SAIGON DEN VUNG TAU, CÓ LỊCH CHẠY NGÀY $realDayFromDate = 1
        $products = $this->productService->getProductByConditions([
            'direction' => DirectionTypeEnum::SGVT,
            'directionReturn' => DirectionTypeEnum::VTSG,
            'day' => $realDayFromDate,
            'date' => $fromDate,
            'isCurrentDay' => $fromCurrentDay,
            'directionTime' => 'start_time_sg_vt',
            'brands' => $selectedBrands,
            'fillters' => $filterDetailIds,
            'times' => $times,
            'prices' => $prices,
            'locationCheckedStarts' => $locationCheckedStarts,
            'locationCheckedReturns' => $locationCheckedReturns
        ]);
        $returnProducts = collect();
        if ($realDayToDate && $toDate) {
            // lẤY CAC XE TINH TRANG CHAY TU  VUNG TAU DEN SAIGON, CÓ LỊCH CHẠY NGÀY $realDayToDate = 1
            $returnProducts = $this->productService->getProductByConditions([
                'direction' => DirectionTypeEnum::VTSG,
                'directionReturn' => DirectionTypeEnum::SGVT,
                'day' => $realDayToDate,
                'date' => $toDate,
                'isCurrentDay' => $toCurrentDay,
                'directionTime' => 'start_time_vt_sg',
                'brands' => $selectedBrands,
                'fillters' => $filterDetailIds,
                'times' => $times,
                'prices' => $prices,
                'locationCheckedStarts' => $locationCheckedStarts,
                'locationCheckedReturns' => $locationCheckedReturns
            ]);
        }
        $type = $request->type ?? 'replace';
        $settingGeneral = getOption('general', getLocale());
        $meta_seo = metaSeo('', '', [
            'title' => $settingGeneral['sg_vt_title'] ?? __('Đặt vé xe Sài Gòn - Vũng Tàu'),
            'description' => $settingGeneral['sg_vt_description'] ?? __('Đặt vé xe Sài Gòn - Vũng Tàu'),
            'social_title' => $settingGeneral['sg_vt_title'] ?? __('Đặt vé xe Sài Gòn - Vũng Tàu'),
            'social_description' => $settingGeneral['sg_vt_description'] ?? __('Đặt vé xe Sài Gòn - Vũng Tàu'),
        ]);
        $isShowReturn = $request->get('isShowReturn', 0);
        if ($isShowReturn == 1) {
            $products = $returnProducts;
        }
        $compact = compact(
            'meta_seo',
            'products',
            'returnProducts',
            'fromDate',
            'toDate',
            'realDayFromDate',
            'realDayToDate',
            'dataRequest',
            'brands',
            'filters',
            'type',
            'locationStarts',
            'locationReturns',
            'selectedBrands',
            'selectedFilterDetails',
            'filterDetailIds',
            'times',
            'prices',
            'locationCheckedStarts',
            'locationCheckedReturns',
            'isShowReturn'
        );
        if ($request->ajax()) {
            $html = view('search.result', $compact)->render();
            $topHtml = view('search.result-top', $compact)->render();
            return response()->json([
                'error' => false,
                'html' => $html,
                'topHtml' => $topHtml,
                'type' => $type
            ]);
        }
        return view('search.index', $compact);
    }

    private function prepareData(Request $request)
    {
        $dataRequest = $request->all();
        $fromDate = $dataRequest['fromDate'] ?? null;
        $toDate = $dataRequest['toDate'] ?? null;
        $extractFromDate = explode(',', $fromDate);
        $extractToDate = explode(',', $toDate);

        $dayFromDate = trim($extractFromDate[0] ?? null);
        $dateFromDate = trim($extractFromDate[1] ?? null);
        $dayToDate = trim($extractToDate[0] ?? null);
        $dateToDate = trim($extractToDate[1] ?? null);
        if ($dayFromDate) {
            $realDayFromDate = match ($dayFromDate) {
                'CN' => 'sunday',
                'T2' => 'monday',
                'T3' => 'tuesday',
                'T4' => 'wednesday',
                'T5' => 'thursday',
                'T6' => 'friday',
                'T7' => 'saturday',
            };
        } else {
            $realDayFromDate = null;
        }
        if ($dayToDate) {
            $realDayToDate = match ($dayToDate) {
                'CN' => 'sunday',
                'T2' => 'monday',
                'T3' => 'tuesday',
                'T4' => 'wednesday',
                'T5' => 'thursday',
                'T6' => 'friday',
                'T7' => 'saturday',
            };
        } else {
            $realDayToDate = null;
        }

        $fromDate = !empty($dateFromDate) ? \Carbon\Carbon::createFromFormat('d/m/Y', $dateFromDate)->format('Y-m-d') : null;
        $toDate = !empty($dateToDate) ? \Carbon\Carbon::createFromFormat('d/m/Y', $dateToDate)->format('Y-m-d') : null;
        // kiem tra co fromDate va co phai la ngay hom nay
        $fromCurrentDay = $fromDate && $fromDate == date('Y-m-d');
        $toCurrentDay = $toDate && $toDate == date('Y-m-d');

        $brands = $this->brandService->getWithMultiFromConditions([], ['status' => ['=' => BaseStatusEnum::ACTIVE]], 'id', 'desc');
        $filters = $this->filterService->getWithMultiFromConditions(['filterDetail'], ['status' => ['=' => BaseStatusEnum::ACTIVE]], 'id', 'desc');

        $locations = app(LocationServiceInterface::class)->getWithMultiFromConditions([], ['status' => ['=' => BaseStatusEnum::ACTIVE]], 'id', 'desc');
        if ($dataRequest['from'] === 'saigon') {
            $locationStarts = $locations->where('from', LocationEnum::SG);
            $locationReturns = $locations->where('from', LocationEnum::VT);
        } else {
            $locationStarts = $locations->where('from', LocationEnum::VT);
            $locationReturns = $locations->where('from', LocationEnum::SG);
        }
        $selectedBrands = [];
        $selectedFilterDetails = [];
        $filterDetailIds = [];
        $times = [];
        $prices = [];
        $locationCheckedStarts = [];
        $locationCheckedReturns = [];
        if ($request->has('minPrice') && $request->has('maxPrice') && ($request->get('minPrice') !== '0' || $request->get('maxPrice') !== '2000000')) {
            $prices = [
                $request->get('minPrice'),
                $request->get('maxPrice')
            ];
        }
        if ($request->has('startTime') && $request->has('endTime') && ($request->get('startTime') !== '00:00' || $request->get('endTime') !== '24:00')) {
            $times = [
                date('H:i:s', strtotime($request->get('startTime') . ':00')),
                date('H:i:s', strtotime(($request->get('endTime') === '24:00' ? '23:59' : $request->get('endTime')) . ':00'))
            ];
        }
        if ($request->has('selectedFillters')) {
            $selectedFilters = $request->get('selectedFillters', []);

            foreach ($selectedFilters as $filter) {
                if (str_starts_with($filter, 'locationStarts[')) {
                    preg_match('/locationStarts\[(\d+)\]/', $filter, $matches);
                    if (isset($matches[1])) {
                        $locationCheckedStarts[] = $matches[1];
                    }
                } else if (str_starts_with($filter, 'locationReturns[')) {
                    preg_match('/locationReturns\[(\d+)\]/', $filter, $matches);
                    if (isset($matches[1])) {
                        $locationCheckedReturns[] = $matches[1];
                    }
                } else if (str_starts_with($filter, 'brands[')) {
                    preg_match('/brands\[(\d+)\]/', $filter, $matches);
                    if (isset($matches[1])) {
                        $selectedBrands[] = $matches[1];
                    }
                } elseif (str_starts_with($filter, 'filter[')) {
                    preg_match('/filter\[(\d+)\]\[(\d+)\]/', $filter, $matches);
                    if (isset($matches[1]) && isset($matches[2])) {
                        $filterId = $matches[1];
                        $filterDetailId = $matches[2];
                        if (!isset($selectedFilterDetails[$filterId])) {
                            $selectedFilterDetails[$filterId] = [];
                        }
                        $selectedFilterDetails[$filterId][] = $filterDetailId;
                        $filterDetailIds[] = $filterDetailId;
                    }
                }
            }
        }

        return [
            $fromDate,
            $toDate,
            $realDayFromDate,
            $realDayToDate,
            $fromCurrentDay,
            $toCurrentDay,
            $dataRequest,
            $brands,
            $filters,
            $selectedBrands,
            $selectedFilterDetails,
            $filterDetailIds,
            $times,
            $prices,
            $locationStarts,
            $locationReturns,
            $locationCheckedStarts,
            $locationCheckedReturns
        ];
    }

    public function placeOrder(Request $request)
    {
        $data = $request->all();
        $from = $data['departure'] ?? null;
        $to = $data['destination'] ?? null;
        $departureDate = $data['departureDate'] ?? null;
        $returnDate = $data['returnDate'] ?? null;
        $extractFromDate = explode(',', $departureDate);
        $extractToDate = explode(',', $returnDate);
        $dateFromDate = trim($extractFromDate[1] ?? null);
        $dateToDate = trim($extractToDate[1] ?? null);
        $productId = $data['productId'] ?? null;
        $quantity = $data['quantity'] ?? null;
        // validate rules và báo khi validate failed
        $rules = [
            'departure' => 'required',
            'destination' => 'required',
            'departureDate' => 'required',
            'productId' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'name' => 'required',
            'phone' => 'required|phone_number',
            'cccd' =>  ['required', 'digits:12'],
            'pickUpId' => 'required',
            'dropOffId' => 'required',
            'pickUpAddress' => 'nullable',
            'dropOffAddress' => 'nullable',
        ];
        $messages = [
            'departure.required' => 'Vui lòng chọn điểm đi.',
            'destination.required' => 'Vui lòng chọn điểm đến.',
            'departureDate.required' => 'Vui lòng chọn ngày khởi hành.',
            'departureDate.date_format' => 'Ngày khởi hành không đúng định dạng.',
            'returnDate.date_format' => 'Ngày về không đúng định dạng.',
            'productId.required' => 'Vui lòng chọn xe.',
            'productId.exists' => 'Xe không tồn tại.',
            'quantity.required' => 'Vui lòng nhập số lượng.',
            'quantity.integer' => 'Số lượng phải là một số nguyên.',
            'quantity.min' => 'Số lượng tối thiểu là 1.',
            'name.required' => 'Vui lòng nhập họ tên.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.phone_number' => 'Số điện thoại không đúng định dạng.',
            'cccd.required' => "Bạn phải nhập số căn cước",
            'cccd:digits' => "Số căn cước không đúng định dạng",
            'pickUpId.required' => 'Vui lòng chọn địa điểm đón.',
            'dropOffId.required' => 'Vui lòng chọn địa điểm trả.',
        ];
        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'type' => 'validation',
                'message' => $validator->errors()->first()
            ]);
        }

        $pickUpId = $data['pickUpId'] ?? 0;
        $dropOffId = $data['dropOffId'] ?? 0;
        $pickUpAddress = $data['pickUpAddress'] ?? null;
        $dropOffAddress = $data['dropOffAddress'] ?? null;

        if ($from === 'saigon') {
            $direction = DirectionTypeEnum::SGVT;
        } else {
            $direction = DirectionTypeEnum::VTSG;
        }
        DB::beginTransaction();
        try {
            $product = $this->productService->findOne(['id' => $productId]);
            $product->load('productLocations');
            $productLocations = $product->productLocations;
            $pickupLocation = $productLocations->where('id', $pickUpId)->first();
            $dropoffLocation = $productLocations->where('id', $dropOffId)->first();
            if ($pickupLocation && $pickupLocation->transit && empty($pickUpAddress)) {
                return response()->json([
                    'error' => true,
                    'type' => 'validation',
                    'message' => 'Vui lòng nhập địa điểm đón chi tiết.'
                ]);
            }
            if ($dropoffLocation && $dropoffLocation->transit && empty($dropOffAddress)) {
                return response()->json([
                    'error' => true,
                    'type' => 'validation',
                    'message' => 'Vui lòng nhập địa điểm trả chi tiết.'
                ]);
            }

            $price = $product->price;
            $totalPrice = $price * $quantity;
            $fromDate = !empty($dateFromDate) ? \Carbon\Carbon::createFromFormat('d/m/Y', $dateFromDate)->format('Y-m-d') : null;
            $toDate = !empty($dateToDate) ? \Carbon\Carbon::createFromFormat('d/m/Y', $dateToDate)->format('Y-m-d') : null;

            // Tạo đơn hàng chi tiết
            $orderDetail = [
                'product_id' => $productId,
                'product_name' => $product->name,
                'location_product_id' => $pickUpId,
                'return_location_product_id' => $dropOffId,
                'location_pickup_detail' => $pickUpAddress,
                'location_return_detail' => $dropOffAddress,
                'quantity' => $quantity,
                'price' => $price,
                'direction' => $direction,
                'start_date' => $fromDate,
                'end_date' => $toDate,
            ];
            $order = [
                'code' => randomCodeOrder(),
                'total_price' => $totalPrice
            ];
            $customer = [
                'name' => $data['name'],
                'phone' => $data['phone'],
                'cccd' => $data['cccd']
            ];
            $order = $this->orderService->createOrder([
                'order' => $order,
                'order_details' => $orderDetail,
                'customer' => $customer
            ]);
            Cache::forget('order_new_number');
            DB::commit();
            return response()->json([
                'error' => false,
                'message' => 'Đặt chỗ thành công. Vui lòng tải vé tại Tra Cứu'
            ]);
        } catch (Exception $e) {
            DB::rollback();
            Log::error($e);
            return response()->json([
                'error' => true,
                'message' => 'Đặt chỗ thất bại. Vui lòng thử lại hoặc liên hệ hotline.'
            ]);
        }
    }
    public function customerSlot(Request $request)
    {
        $data = $request->all();
        $rules = [
            'phone' => 'required|phone_number',
            'cccd' =>  ['required', 'digits:12']
        ];
        $messages = [
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.phone_number' => 'Số điện thoại không đúng định dạng.',
            'cccd.required' => "Bạn phải nhập số căn cước",
            'cccd:digits' => "Số căn cước không đúng định dạng"
        ];
        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'type' => 'validation',
                'message' => $validator->errors()->first()
            ]);
        }
        try {
            $customers = $this->orderService->getCustomerOrder($data['cccd'], $data['phone']);
            $customer = $customers->first();
            // Thu thập tất cả các order từ mảng customers
            $allOrders = $customers->map(function ($customer) {
                return $customer->order;
            })->filter(); // Loại bỏ order null nếu có

            // Gán danh sách order vào thuộc tính allOrders
            $customer->order = $allOrders;

            $html = view('home.ve', compact('customer'))->render();

            return response()->json([
                'error' => false,
                'html' => $html,
                'message' => 'Thành công'
            ]);
        } catch (Exception $e) {
            DB::rollback();
            Log::error($e);
            return response()->json([
                'error' => true,
                'message' => 'Lấy thông tin thất bại. Vui lòng thử lại hoặc liên hệ hotline.'
            ]);
        }
    }
    public function download($code)
    {

        try {
            $order = $this->orderService->findOne(['code' => $code]);
            $data = [
                'name' => $order->customer->name,
                'phone' => $order->customer->phone,
                'cccd' => $order->customer->cccd,
                'route' => ($order->orderDetail->first()->direction == "vt_sg" ? "Vũng Tàu - Sài Gòn" : "Sài Gòn - Vũng Tàu"),
                'number' =>  $order->orderDetail->first()->product->brand->name,
                'date' => date('d/m/Y', strtotime($order->orderDetail->first()->start_date ?? 'now')),
                'pickup' => $order->orderDetail->first()->productLocation->location->name ?? '',
                'dropoff' => $order->orderDetail->first()->productLocationReturn->location->name ?? '',
            ];

            // Render view PDF
            $pdf = Pdf::loadView('home.pdf', $data);

            return $pdf->download('ve-xe-' . $order->code . '.pdf');
        } catch (Exception $e) {
            DB::rollback();
            Log::error($e);
            return response()->json([
                'error' => true,
                'message' => 'Lấy thông tin thất bại. Vui lòng thử lại hoặc liên hệ hotline.'
            ]);
        }
    }
}
