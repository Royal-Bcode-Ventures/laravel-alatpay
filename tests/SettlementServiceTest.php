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

it('lists settlements for the configured business id', function () {
    Http::fake([
        '*/payment-settlement/api/v1/settlements*' => Http::response([
            'status' => true,
            'message' => 'Success',
            'data' => ['id' => 'settlement-1', 'amount' => 5000],
            'pagination' => ['currentPage' => 1, 'totalItems' => 10, 'totalPages' => 1],
        ], 200),
    ]);

    $response = AlatPay::settlements()->all();

    expect($response['status'])->toBeTrue();

    Http::assertSent(fn ($request) => str_contains($request->url(), 'businessId=test-business-id'));
});

it('applies optional filters when listing settlements', function () {
    Http::fake([
        '*/payment-settlement/api/v1/settlements*' => Http::response([
            'status' => true,
            'message' => 'Success',
            'data' => [],
        ], 200),
    ]);

    AlatPay::settlements()->all([
        'status' => 'settled',
        'startAt' => '2026-01-01',
        'endAt' => '2026-01-31',
    ]);

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'status=settled')
            && str_contains($request->url(), 'startAt=2026-01-01')
            && str_contains($request->url(), 'endAt=2026-01-31');
    });
});
