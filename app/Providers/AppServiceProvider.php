<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
       Validator::extend('phone_number', function($attribute, $value, $parameters){
            $sub = substr($value, 0, 2);
            $len = strlen($value);
            if( ($sub == '03' || $sub == '05' || $sub == '07' || $sub == '08' || $sub == '09' ) && $len == 10 ){
                return true;
            }
        });
    }
}
