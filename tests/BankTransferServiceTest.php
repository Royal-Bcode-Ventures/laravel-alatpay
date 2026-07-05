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
use RoyalBcode\AlatPay\Exceptions\AlatPayException;
use RoyalBcode\AlatPay\Facades\AlatPay;

it('generates a virtual account successfully', function () {
    Http::fake([
        '*/bank-transfer/api/v1/bankTransfer/virtualAccount' => Http::response([
            'status' => true,
            'message' => 'Successful',
            'data' => [
                'transactionId' => 'txn-123',
                'virtualBankAccountNumber' => '8880007577',
                'virtualBankCode' => '035',
            ],
        ], 200),
    ]);

    $response = AlatPay::bankTransfer()->generateVirtualAccount([
        'amount' => 100,
        'orderId' => 'order-1',
        'description' => 'Test payment',
        'customer' => [
            'email' => 'johndoe@email.com',
            'phone' => '08000000001',
            'firstName' => 'John',
            'lastName' => 'Doe',
        ],
    ]);

    expect($response['status'])->toBeTrue()
        ->and($response['data']['transactionId'])->toBe('txn-123');

    Http::assertSent(function ($request) {
        return $request['businessId'] === 'test-business-id'
            && $request['currency'] === 'NGN'
            && $request->hasHeader('Ocp-Apim-Subscription-Key', 'test-secret-key');
    });
});

it('confirms a bank transfer transaction status', function () {
    Http::fake([
        '*/bank-transfer/api/v1/bankTransfer/transactions/*' => Http::response([
            'status' => true,
            'message' => 'Successful',
            'data' => ['id' => 'txn-123', 'status' => 'completed'],
        ], 200),
    ]);

    $response = AlatPay::bankTransfer()->confirmTransaction('txn-123');

    expect($response['data']['status'])->toBe('completed');
});

it('throws an AlatPayException on a failed request', function () {
    Http::fake([
        '*/bank-transfer/api/v1/bankTransfer/virtualAccount' => Http::response([
            'status' => false,
            'message' => 'Invalid business ID',
        ], 400),
    ]);

    AlatPay::bankTransfer()->generateVirtualAccount([
        'amount' => 100,
        'orderId' => 'order-1',
        'customer' => ['email' => 'a@b.com', 'phone' => '080', 'firstName' => 'A', 'lastName' => 'B'],
    ]);
})->throws(AlatPayException::class, 'Invalid business ID');
