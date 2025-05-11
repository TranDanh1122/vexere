<?php

namespace DreamTeam\Ecommerce\Models;

use DreamTeam\Base\Models\BaseModel;
use DreamTeam\Location\Models\Province;
use DreamTeam\Location\Models\District;
use DreamTeam\Location\Models\Ward;
use DreamTeam\Location\Models\Country;
use DreamTeam\Location\Facades\Location;

class Customer extends BaseModel {

    protected $guarded = ['id'];

    public function order() {
        return $this->hasOne(Order::class, 'customer_id', 'id');
    }

    public function country() {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function district() {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    public function province() {
        return $this->belongsTo(Province::class, 'province_id', 'id');
    }

    public function ward() {
        return $this->belongsTo(Ward::class, 'ward_id', 'id');
    }

    public function getAddress()
    {
        $address = $this->address;
        $str = '';
        if(!empty($this->ward)) {
            $str .= $this->ward->name;
        }

        if(!empty($this->district)) {
            $str .= ' - ' . $this->district->name;
        }

        if(!empty($this->province)) {
            $str .= ' - ' . $this->province->name;
        }

        if(!empty($this->country)) {
            $str .= ' - ' . $this->country->name;
        }
        return $address . ' - ' . $str;
    }
}
