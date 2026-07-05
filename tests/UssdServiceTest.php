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

it('initiates a USSD phone payment', function () {
    Http::fake([
        '*/alatpay-phone-number/api/v1/phone-number-payment/initialize' => Http::response([
            'status' => true,
            'message' => 'Successfully initiated. Please, complete your payment process.',
            'data' => ['transactionId' => 'ussd-txn-1', 'phoneNumber' => '081******86'],
        ], 200),
    ]);

    $response = AlatPay::ussd()->initiate([
        'amount' => 250,
        'customer' => [
            'email' => 'example@example.com',
            'phone' => '1234567890',
            'firstName' => 'John',
            'lastName' => 'Doe',
        ],
        'phonenumber' => '0987654321',
    ]);

    expect($response['data']['transactionId'])->toBe('ussd-txn-1');
});

it('validates and completes a USSD payment', function () {
    Http::fake([
        '*/alatpay-phone-number/api/v1/phone-number-payment/complete-phonenumber-payment' => Http::response([
            'status' => true,
            'message' => 'Success',
            'data' => ['id' => 'txn-99', 'status' => 'completed'],
        ], 200),
    ]);

    $response = AlatPay::ussd()->validateAndPay([
        'phonenumber' => '08134529895',
        'amount' => 100,
        'transactionId' => 'ussd-txn-1',
    ]);

    expect($response['data']['status'])->toBe('completed');
});
