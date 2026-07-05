<?php

/*
|--------------------------------------------------------------------------
| Laravel ALATPay SDK
|--------------------------------------------------------------------------
|
| Package:
| royalbcode/laravel-alatpay
|
| Developed by Royal Bcode Ventures Ltd
| Lead Developer: Gift Balogun
| https://royalbv.name.ng
|
| This project is an independent open-source Laravel SDK for ALATPay.
| It is not affiliated with or endorsed by Wema Bank PLC or ALATPay.
|
*/

namespace RoyalBcode\AlatPay;

use Illuminate\Support\ServiceProvider;

class AlatPayServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/alatpay.php', 'alatpay');

        $this->app->singleton(AlatPay::class, function ($app) {
            return new AlatPay($app['config']->get('alatpay', []));
        });

        $this->app->alias(AlatPay::class, 'alatpay');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/alatpay.php' => config_path('alatpay.php'),
            ], 'alatpay-config');
        }
    }

    public function provides(): array
    {
        return [AlatPay::class, 'alatpay'];
    }
}
