<?php

namespace DreamTeam\Ecommerce\Services;

use DreamTeam\Ecommerce\Repositories\Interfaces\OrderRepositoryInterface;
use DreamTeam\Ecommerce\Repositories\Interfaces\OrderDetailRepositoryInterface;
use DreamTeam\Ecommerce\Repositories\Interfaces\OrderHistoryRepositoryInterface;
use DreamTeam\Ecommerce\Repositories\Interfaces\CustomerRepositoryInterface;
use DreamTeam\Ecommerce\Services\Interfaces\OrderServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use DreamTeam\Base\Services\CrudService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use DreamTeam\Base\Events\ClearCacheEvent;

class OrderService extends CrudService implements OrderServiceInterface
{

    protected OrderDetailRepositoryInterface $orderDetailRepository;
    protected OrderHistoryRepositoryInterface $orderHistoryRepository;
    protected CustomerRepositoryInterface $customerRepository;

    public function __construct(
        OrderRepositoryInterface $repository,
        OrderDetailRepositoryInterface $orderDetailRepository,
        OrderHistoryRepositoryInterface $orderHistoryRepository,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->repository = $repository;
        $this->orderDetailRepository = $orderDetailRepository;
        $this->orderHistoryRepository = $orderHistoryRepository;
        $this->customerRepository = $customerRepository;
    }

    public function getOrderHistory($conditions, $orderByColumn, $orderByValue): ?Collection
    {
        return $this->orderHistoryRepository->getMultipleFromConditions([], $conditions, $orderByColumn, $orderByValue);
    }

    public function addOrderHistory($orderID, $type, $data = []): Model
    {
        if (!empty($data)) {
            $data = base64_encode(json_encode($data));
        } else {
            $data = null;
        }
        return $this->orderHistoryRepository->createOrUpdateFromArray([
            'order_id' => $orderID,
            'admin_user_id' => \Auth::guard('admin')->user()->id ?? 0,
            'type' => $type,
            'data' => $data,
            'time' => date('Y-m-d H:i:s'),
        ], false);
    }

    public function createOrder($data): Model
    {
        $customer = $this->customerRepository->createOrUpdateFromArray($data['customer'], false);
        if ($customer) {
            $data['order']['customer_id'] = $customer->id;
        }
        $order = $this->repository->createOrUpdateFromArray($data['order'], false);
        if ($order) {
            $data['order_details']['order_id'] = $order->id;
            $this->addOrderHistory($order->id, 'created', []);
            $this->orderDetailRepository->createOrUpdateFromArray($data['order_details'], false);
        }
        $this->sendMailAfterOrder($order->code);
        return $order;
    }

    public function sendMailAfterOrder($orderCode)
    {
        event(new ClearCacheEvent());
        $settingMail = getOption('email', null, false);
        if (isset($settingMail['smtp_username']) && !empty($settingMail['smtp_username']) && isset($settingMail['smtp_password']) && !empty($settingMail['smtp_password'])) {
            $order = $this->repository->findOneWithFromArray(['customer', 'orderDetail'], ['code' => $orderCode], false);
            try {
                $emailAdmin = $settingMail['smtp_email_reply_to'] ?? '';
                if (!empty($emailAdmin)) {
                    Log::info('start send mail to admin after order sucess');
                    Mail::to($emailAdmin)->send(new \DreamTeam\Ecommerce\Mail\NotificationOrderAdmin($order));
                    Log::info('Done send mail to admin after order sucess');
                }
            } catch (\Exception $e) {
                Log::error('Send mail order admin faild ' . $e->getMessage());
            }
        } else {
            Log::error('Chưa cấu hình server mail');
        }
    }

    public function getCustomerOrder($cccd, $phone)
    {
        return $this->customerRepository->getMultipleFromConditions(['order', 'order.orderDetail', 'order.orderDetail.product', 'order.orderDetail.product.productLocations', 'order.orderDetail.product' , 'order.orderDetail.productLocation.location' , 'order.orderDetail.productLocationReturn.location'], ['cccd' => $cccd,  'phone' => $phone], 'created_at', 'desc');
    }
}
