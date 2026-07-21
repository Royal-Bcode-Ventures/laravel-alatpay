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

use Illuminate\Support\Facades\Http;
use RoyalBcode\AlatPay\Facades\AlatPay;
use RoyalBcode\AlatPay\Services\PaymentLinkService;

it('creates a payment link with sensible defaults', function () {
    Http::fake([
        '*/merchant-onboarding/api/v1/payment/initialize' => Http::response([
            'status' => true,
            'message' => 'Success',
            'data' => [
                'id' => 'link-1',
                'paymentUrl' => 'https://paylink.alatpay.ng/?reference=payAbPTX6l4TUbn',
                'paymentReference' => 'payAbPTX6l4TUbn',
                'redirectUrl' => 'https://www.mywebsite.ng/success',
            ],
        ], 200),
    ]);

    $response = AlatPay::paymentLink()->create([
        'email' => 'johndoe@gmail.com',
        'redirectUrl' => 'https://www.mywebsite.ng/success',
        'amount' => 100,
    ]);

    expect($response['data']['paymentReference'])->toBe('payAbPTX6l4TUbn');

    Http::assertSent(function ($request) {
        return $request['currency'] === PaymentLinkService::CURRENCY_NGN
            && $request['passCharge'] === false;
    });
});

it('respects an explicit passCharge override', function () {
    Http::fake([
        '*/merchant-onboarding/api/v1/payment/initialize' => Http::response([
            'status' => true,
            'message' => 'Success',
            'data' => ['paymentReference' => 'pay-2'],
        ], 200),
    ]);

    AlatPay::paymentLink()->create([
        'email' => 'jane@example.com',
        'redirectUrl' => 'https://example.com/success',
        'amount' => 5000,
        'passCharge' => true,
    ]);

    Http::assertSent(fn ($request) => $request['passCharge'] === true);
});

it('checks a payment link transaction status by reference', function () {
    Http::fake([
        '*/merchant-onboarding/api/v1/payment/status/payicHBqRI6jEo7' => Http::response([
            'status' => true,
            'message' => 'Success',
            'data' => ['status' => 'completed', 'paymentLinkReference' => 'payicHBqRI6jEo7'],
        ], 200),
    ]);

    $response = AlatPay::paymentLink()->status('payicHBqRI6jEo7');

    expect($response['data']['status'])->toBe('completed');
});