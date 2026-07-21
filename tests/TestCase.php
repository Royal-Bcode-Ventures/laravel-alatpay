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

namespace RoyalBcode\AlatPay\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use RoyalBcode\AlatPay\AlatPayServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [AlatPayServiceProvider::class];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'AlatPay' => \RoyalBcode\AlatPay\Facades\AlatPay::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('alatpay.secret_key', 'test-secret-key');
        $app['config']->set('alatpay.public_key', 'test-public-key');
        $app['config']->set('alatpay.business_id', 'test-business-id');
        $app['config']->set('alatpay.base_url', 'https://apibox.alatpay.ng');
        $app['config']->set('alatpay.timeout', 30);
        $app['config']->set('alatpay.pass_charge', false);
    }
}
