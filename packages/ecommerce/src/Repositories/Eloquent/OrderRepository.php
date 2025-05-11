<?php

namespace DreamTeam\Ecommerce\Repositories\Eloquent;

use DreamTeam\Base\Repositories\Eloquent\BaseRepository;
use DreamTeam\Ecommerce\Models\Order;
use DreamTeam\Ecommerce\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use DreamTeam\Ecommerce\Enums\OrderStatusEnum;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    protected string|null|Model $model = Order::class;

    public function getOrderByconditionsToExports(bool $hasPayment, array $inputData, string $select = 'orders.*'): Collection
    {
        $datas = $this->getModel()->with(['customer'])
            ->whereIn('orders.status', OrderStatusEnum::toArray());
        extract($inputData, EXTR_OVERWRITE);

        if (isset($code) && !empty($code)) {
            $datas = $datas->where('code',$code);
        }
        if ($hasPayment) {
            $datas = $datas->with('payment')->leftJoin('payments', 'payments.id', 'orders.payment_id');
        }
        if ($hasPayment && isset($payment_method) && !empty($payment_method)) {
            $datas = $datas->where('payments.payment_channel', $payment_method);
        }
        if ($hasPayment && isset($payment_status)) {
            $datas = $datas->where('payments.status', $payment_status);
        }
        if ((isset($customer_phone) && !empty($customer_phone) || (isset($customer_name) && $customer_name != ''))) {
            $datas = $datas->join('customers', 'customers.id', 'orders.customer_id');
        }
        if (isset($customer_phone) && !empty($customer_phone)) {
            $datas = $datas->where('customers.phone', $customer_phone);
        }
        if (isset($customer_name) && $customer_name != '') {
            $datas = $datas->where('customers.name', 'LIKE', "%".str_replace(' ', '%', $customer_name).'%');
        }
        if (isset($order_status) && !empty($order_status)) {
            $datas = $datas->where('orders.status', $order_status);
        }
        if (isset($created_at_start) && !empty($created_at_start)) {
            $datas = $datas->whereBetween('orders.created_at', [$created_at_start, $created_at_end]);
        }
        return $datas->selectRaw($select)->orderBy('orders.id', 'desc')->get();
    }
}
