<?php

namespace DreamTeam\Ecommerce\Models;

use DreamTeam\Base\Models\BaseModel;
use DreamTeam\Ecommerce\Enums\OrderStatusEnum;

class Order extends BaseModel
{
    protected $guarded = ['id'];

    protected $casts = [
    	'status' => OrderStatusEnum::class
    ];

	public function getTotalPrice()
    {
		return formatPrice(
			$this->total_price + ($this->shipping->shipping_amount ?? 0)
			, null);
	}

	public function getTotal()
    {
		return $this->total_price + ($this->shipping->shipping_amount ?? 0);
	}

	public function getStatusLabel()
	{
		return $this->status->toHtml();
	}

	public function getStatus()
    {
		return [
			'status' 		=> $this->status,
			'status_text' 	=> $this->status->label(),
			'status_label' 	=> $this->status->toHtml(),
		];
	}

    public function orderDetail()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function getCustomerEmail()
    {
        return $this->customer->email ?? '';
    }

    public function getCustomerName()
    {
        return $this->customer->name ??  __('Ecommerce::admin.not_provided');
    }

    public function getCustomerPhone()
    {
        return $this->customer->phone ??  __('Ecommerce::admin.not_provided');
    }

}
