<?php

namespace DreamTeam\Ecommerce\Services\Interfaces;

use DreamTeam\Base\Services\Interfaces\CrudServiceInterface;

interface OrderServiceInterface extends CrudServiceInterface
{
    public function getOrderHistory($conditions, $orderByColumn, $orderByValue);
    public function addOrderHistory($orderID, $type, $data = []);
    public function createOrder($data);
    public function getCustomerOrder($cccd , $phone);
}
