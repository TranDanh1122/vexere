<?php

namespace DreamTeam\Ecommerce\Repositories\Eloquent;

use DreamTeam\Base\Repositories\Eloquent\BaseRepository;
use DreamTeam\Ecommerce\Models\OrderDetail;
use DreamTeam\Ecommerce\Repositories\Interfaces\OrderDetailRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use DreamTeam\Ecommerce\Enums\OrderStatusEnum;

class OrderDetailRepository extends BaseRepository implements OrderDetailRepositoryInterface
{
    protected string|null|Model $model = OrderDetail::class;

    public function getOrderDetailByConditionToExports(bool $hasPayment, array $inputData): Collection
    {
        $data = $this->getModel()
            ->with(['product', 'order.customer', 'order.customer.country', 'order.customer.province', 'order.customer.district', 'order.customer.ward'])
            ->whereIn('orders.status', OrderStatusEnum::toArray());
        extract($inputData, EXTR_OVERWRITE);
        $data = $data->join('orders', 'orders.id', 'order_details.order_id');
        if ($hasPayment && isset($payment_method) || isset($payment_status)) {
            $data = $data->join('payments', 'payments.id', 'orders.payment_id');
            
            if (isset($payment_method) && !empty($payment_method)) {
                $data = $data->where('payments.payment_channel', $payment_method);
            }
            if (isset($payment_status) && !empty($payment_status)) {
                $data = $data->where('payments.status', $payment_status);
            }
        }
        if (isset($customer_name) || isset($customer_phone)) {
            $data = $data->join('customers', 'customers.id', 'orders.customer_id');

            if (isset($customer_name) && $customer_name != '') {
                $data = $data->where('customers.name', 'LIKE', "%".str_replace(' ', '%', $customer_name).'%');
            }
            if (isset($customer_phone) && $customer_phone != '') {
                $data = $data->where('customers.phone', $customer_phone);
            }
        }
        if(isset($order_code) && $order_code) {
            $data = $data->where('orders.code', $order_code);
        }
        if(isset($status) && $status) {
            $data = $data->where('orders.status', $status);
        }
        if(isset($created_at_start) && !empty($created_at_start) && isset($created_at_end) && !empty($created_at_end)) {
            $data = $data->whereBetween('orders.created_at', [$created_at_start, $created_at_end]);
        }
        if(isset($product_name) && !empty($product_name)) {
            $data = $data->where('order_details.product_name', 'LIKE', "%".str_replace(' ', '%', $product_name).'%');
        }
        return $data->select('order_details.*')->orderBy('orders.id', 'asc')->get();
    }
}
