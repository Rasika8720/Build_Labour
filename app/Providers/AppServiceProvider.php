<?php

namespace App\Providers;

use App\Helpers\Rules;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('image64', function ($attribute, $value, $parameters, $validator) {

            $type = explode('/', explode(':', substr($value, 0, strpos($value, ';')))[1])[1];
            if (in_array($type, $parameters)) {
                return true;
            }
            return false;
        });
    
        Validator::replacer('image64', function($message, $attribute, $rule, $parameters) {
            return str_replace(':values', join(",", $parameters), $message);
        });

        Validator::extend('maxWords', function($attribute, $value, $parameters, $validator) {

            return Rules::maxWords($attribute, $value, $parameters);
        });

        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}