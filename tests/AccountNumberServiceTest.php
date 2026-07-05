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
use RoyalBcode\AlatPay\Services\AccountNumberService;

it('sends an OTP defaulting to the Wema bank code', function () {
    Http::fake([
        '*/alatpayaccountnumber/api/v1/accountNumber/sendOtp' => Http::response([
            'status' => true,
            'message' => 'Success',
            'data' => ['transactionId' => 'acct-txn-1'],
        ], 200),
    ]);

    $response = AlatPay::accountNumber()->sendOtp([
        'amount' => 1000,
        'customer' => [
            'email' => 'jane.joe@email.com',
            'phone' => '+2348000000001',
            'firstName' => 'Jane',
            'lastName' => 'Joe',
        ],
        'accountNumber' => '0123456789',
    ]);

    expect($response['data']['transactionId'])->toBe('acct-txn-1');

    Http::assertSent(fn ($request) => $request['bankCode'] === AccountNumberService::WEMA_BANK_CODE);
});

it('validates the OTP and completes the debit', function () {
    Http::fake([
        '*/alatpayaccountnumber/api/v1/accountNumber/validateAndPay' => Http::response([
            'status' => true,
            'message' => 'Success',
            'data' => ['id' => 'acct-txn-1', 'status' => 'completed'],
        ], 200),
    ]);

    $response = AlatPay::accountNumber()->validateAndPay([
        'otp' => '123456',
        'transactionId' => 'acct-txn-1',
    ]);

    expect($response['data']['status'])->toBe('completed');
});
