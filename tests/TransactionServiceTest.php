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

it('lists transactions with default paging and the configured business id', function () {
    Http::fake([
        '*/alatpaytransaction/api/v1/transactions*' => Http::response([
            'status' => true,
            'message' => 'Success',
            'data' => ['id' => 'txn-1', 'amount' => 5000],
            'pagination' => ['currentPage' => 1, 'totalItems' => 10, 'totalPages' => 1],
        ], 200),
    ]);

    $response = AlatPay::transactions()->all();

    expect($response['status'])->toBeTrue()
        ->and($response['pagination']['totalItems'])->toBe(10);

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'Page=1')
            && str_contains($request->url(), 'BusinessId=test-business-id');
    });
});

it('applies optional filters when listing transactions', function () {
    Http::fake([
        '*/alatpaytransaction/api/v1/transactions*' => Http::response([
            'status' => true,
            'message' => 'Success',
            'data' => [],
        ], 200),
    ]);

    AlatPay::transactions()->all([
        'page' => 2,
        'limit' => 25,
        'status' => 'completed',
        'paymentMethod' => 'BankTransfer',
        'startAt' => '2026-01-01',
        'endAt' => '2026-01-31',
    ]);

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'Page=2')
            && str_contains($request->url(), 'Limit=25')
            && str_contains($request->url(), 'Status=completed')
            && str_contains($request->url(), 'PaymentMethod=BankTransfer')
            && str_contains($request->url(), 'StartAt=2026-01-01')
            && str_contains($request->url(), 'EndAt=2026-01-31');
    });
});

it('retrieves a single transaction by id', function () {
    Http::fake([
        '*/alatpaytransaction/api/v1/transactions/txn-123' => Http::response([
            'status' => true,
            'message' => 'Success',
            'data' => ['id' => 'txn-123', 'status' => 'pending'],
        ], 200),
    ]);

    $response = AlatPay::transactions()->find('txn-123');

    expect($response['data']['id'])->toBe('txn-123');
});
