<?php

namespace DreamTeam\Base\Services;

use Illuminate\Http\Request;
use DreamTeam\AdminUser\Services\Interfaces\AdminUserServiceInterface;
use DreamTeam\Base\Enums\BaseStatusEnum;
use DreamTeam\Base\Services\Interfaces\DashboardServiceInterface;
use DreamTeam\Customer\Services\Interfaces\CustomerLabelServiceInterface;
use DreamTeam\Customer\Services\Interfaces\CustomerServiceInterface;
use DreamTeam\ProductSource\Services\Interfaces\ProductSourceLabelServiceInterface;
use DreamTeam\ProductSource\Services\Interfaces\ProductSourceServiceInterface;

class DashboardService extends CrudService implements DashboardServiceInterface
{
    protected CustomerServiceInterface $customerService;
    protected ProductSourceServiceInterface $productSourceService;
    protected AdminUserServiceInterface $adminUserService;
    protected CustomerLabelServiceInterface $customerLabelService;
    protected ProductSourceLabelServiceInterface $productSourceLabelService;

    public function __construct(
        CustomerServiceInterface $customerService,
        ProductSourceServiceInterface $productSourceService,
        AdminUserServiceInterface $adminUserService,
        CustomerLabelServiceInterface $customerLabelService,
        ProductSourceLabelServiceInterface $productSourceLabelService
    ) {
        $this->customerService = $customerService;
        $this->productSourceService = $productSourceService;
        $this->adminUserService = $adminUserService;
        $this->customerLabelService = $customerLabelService;
        $this->productSourceLabelService = $productSourceLabelService;
    }

    public function dashboard(Request $requests, $currentUser)
    {
        $notLabels = getOption('product_sources')['not_labels'] ?? [];
        $conditions = [];
        if ($notLabels) {
            $conditions = ['callback' => ['CALLBACK' => function($query) use($notLabels) {
                return $query->whereDoesntHave('productSourceMapLabel', function($q) use($notLabels) {
                    return $q->whereIn('product_source_label_id', $notLabels);
                });
            }]];
        }

        $currentUserId = $currentUser->id;

        $staff = $this->adminUserService->getTotalByConditions(['is_supper_admin' => ['DFF' => 1], 'status' => ['=' => BaseStatusEnum::ACTIVE]])->first()->total ?? 0;
        $staffs = $this->adminUserService->getWithMultiFromConditions([], ['status' => ['=' => BaseStatusEnum::ACTIVE]], 'id', 'desc', false, 'id,name,display_name,email');
        $customerLabels = $this->customerLabelService->getWithMultiFromConditions([], ['status' => ['DFF' => BaseStatusEnum::DELETE]], 'id', 'desc', false, 'id,name,color');
        $productSourceLabels = $this->productSourceLabelService->getWithMultiFromConditions([], ['status' => ['DFF' => BaseStatusEnum::DELETE]], 'id', 'desc', false, 'id,name,color');

        $productSourceByLabelConditions = [
            'product_sources.status' => ['DFF' => BaseStatusEnum::DELETE]
        ];
        $conditionNotLabels = $this->productSourceService->callBackNotLabels();
        if ($conditionNotLabels) $productSourceByLabelConditions['notLabels'] = $conditionNotLabels;
        $productSourceByLabels = $this->productSourceService->countProductSourceByLabels($productSourceByLabelConditions)->pluck('total', 'product_source_label_id')->toArray();
        $productSource = $this->productSourceService->getTotalByConditions(['product_sources.status' => ['DFF' => BaseStatusEnum::DELETE]] + $conditions)->first()->total ?? 0;
        $customerByUser = $productSourceByUser = [];
        if ($currentUser->is_supper_admin) {
            $customer = $this->customerService->getTotalByConditions(['customers.status' => ['DFF' => BaseStatusEnum::DELETE]])->first()->total ?? 0;
            $customerByUser = $this->customerService->getTotalByConditions(['customers.status' => ['DFF' => BaseStatusEnum::DELETE]], ['staff_id'])->pluck('total', 'staff_id')->toArray();
            $productSourceByUser = $this->productSourceService->getTotalByConditions(['product_sources.status' => ['DFF' => BaseStatusEnum::DELETE]], ['created_by'])->pluck('total', 'created_by')->toArray();
            $customerByLabels = $this->customerService->countCustomerByLabels(['customers.status' => ['DFF' => BaseStatusEnum::DELETE]])->pluck('total', 'customer_label_id')->toArray();
        } else {
            $customer = $this->customerService->getTotalByConditions([
                'customers.status' => ['DFF' => BaseStatusEnum::DELETE],
                'customers.staff_id' => ['=' => $currentUserId]
            ])->first()->total ?? 0;
            $customerByLabels = $this->customerService->countCustomerByLabels([
                'customers.status' => ['DFF' => BaseStatusEnum::DELETE],
                'customers.staff_id' => ['=' => $currentUserId]
            ])->pluck('total', 'customer_label_id')->toArray();
        }
        $customerName = [];
        $usedColors = []; // Mảng để lưu trữ các mã màu đã sử dụng
        foreach ($customerByUser as $customerId => $total) {
            $colorCode = generateUniqueColor($usedColors); // Hàm tạo màu không trùng
            $usedColors[] = $colorCode;
            $customerName[$colorCode] = $staffs->where('id', $customerId)->first()->getName();
        }
        $customerNameByProduct = [];
        $usedColors = []; // Mảng để lưu trữ các mã màu đã sử dụng
        foreach ($productSourceByUser as $customerId => $total) {
            $colorCode = generateUniqueColor($usedColors); // Hàm tạo màu không trùng
            $usedColors[] = $colorCode;
            $customerNameByProduct[$colorCode] = $staffs->where('id', $customerId)->first()->getName();
        }
        $labelNameByCustomer = [];
        $usedColors = [];
        $filterCustomerByColors = [];
        foreach ($customerByLabels as $customerLabelId => $total) {
            $label = $customerLabels->where('id', $customerLabelId)->first();
            $colorCode = $label->color ?? generateUniqueColor($usedColors);
            $usedColors[] = $colorCode;
            $labelNameByCustomer[$colorCode] = $label->getName();
            $filterCustomerByColors[$colorCode] = "&customer_labels=$customerLabelId";
        }
        $lastProductSourceUpdate = $this->productSourceService->getListLastUpdate();
        $labelNameByProductSource = [];
        $usedColors = ['#df692f', '#f1b44c', '#50a5f1', '#f46a6a'];
        $filterProductSourceByColors = [];
        foreach ($productSourceByLabels as $productSourceLabelId => $total) {
            $label = $productSourceLabels->where('id', $productSourceLabelId)->first();
            $colorCode = $label->color ?? generateUniqueColor($usedColors);
            $usedColors[] = $colorCode;
            $labelNameByProductSource[$colorCode] = $label->getName();
            $filterProductSourceByColors[$colorCode] = "&labels=$productSourceLabelId";
        }
        $labelNameByProductSource["#df692f"] = '2 tháng chưa cập nhập';
        $filterProductSourceByColors['#df692f'] = '&updated_at_start=' . now()->subMonths(3)->toDateTimeString() . '&updated_at_end=' . now()->subMonths(2)->toDateTimeString();

        $labelNameByProductSource["#f1b44c"] = '1 tháng chưa cập nhập';
        $filterProductSourceByColors['#f1b44c'] = '&updated_at_start=' . now()->subMonths(2)->toDateTimeString() . '&updated_at_end=' . now()->subMonths(1)->toDateTimeString();

        $labelNameByProductSource["#50a5f1"] = '15 ngày chưa cập nhập';
        $filterProductSourceByColors['#50a5f1'] = '&updated_at_start=' . now()->subMonths(1)->toDateTimeString() . '&updated_at_end=' . now()->subDays(15)->toDateTimeString();

        $labelNameByProductSource["#f46a6a"] = '3 tháng chưa cập nhập';
        $filterProductSourceByColors['#f46a6a'] = '&updated_at_start=' . now()->subMonths(12)->toDateTimeString() . '&updated_at_end=' . now()->subMonths(3)->toDateTimeString();

        $productSourceByLabels['not_updated_2_months'] = $lastProductSourceUpdate['not_updated_2_months'];
        $productSourceByLabels['not_updated_1_month'] = $lastProductSourceUpdate['not_updated_1_month'];
        $productSourceByLabels['not_updated_15_days'] = $lastProductSourceUpdate['not_updated_15_days'];
        $productSourceByLabels['not_updated_3_months'] = $lastProductSourceUpdate['not_updated_3_months'];
        $customerInRanges = $this->customerService->getCustomerCountInRange();

        $compact = compact(
            'staff',
            'staffs',
            'productSource',
            'productSourceByUser',
            'customerNameByProduct',
            'customer',
            'customerByUser',
            'customerName',
            'customerByLabels',
            'labelNameByCustomer',
            'currentUser',
            'customerInRanges',
            'labelNameByProductSource',
            'productSourceByLabels',
            'filterCustomerByColors',
            'filterProductSourceByColors'
        );

        return $compact;

    }
}