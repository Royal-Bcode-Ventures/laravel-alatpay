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
use RoyalBcode\AlatPay\Services\StaticWalletService;

it('creates an individual static wallet and receives an OTP', function () {
    Http::fake([
        '*/alatpay-wallet/api/v1/staticaccount' => Http::response([
            'id' => 'wallet-1',
            'message' => 'An OTP has been sent to 08*******86 for verification.',
            'otpTrackingID' => 'track-1',
        ], 200),
    ]);

    $response = AlatPay::staticWallet()->create([
        'staticWalletType' => StaticWalletService::WALLET_TYPE_INDIVIDUAL,
        'bvn' => '12345678901',
    ]);

    expect($response['otpTrackingID'])->toBe('track-1');
});

it('validates and finalizes static wallet creation', function () {
    Http::fake([
        '*/alatpay-wallet/api/v1/staticaccount/validateAndCreate' => Http::response([
            'accountNumber' => '0412345678',
            'accountName' => 'Your Business - David_Mark',
            'id' => 'wallet-1',
        ], 200),
    ]);

    $response = AlatPay::staticWallet()->validateAndCreate([
        'staticWalletId' => 'wallet-1',
        'otp' => '332610',
        'trackingId' => 'track-1',
    ]);

    expect($response['accountNumber'])->toBe('0412345678');
});

it('lists static wallets with default pagination', function () {
    Http::fake([
        '*/alatpay-wallet/api/v1/staticaccount*' => Http::response([
            'totalCount' => 1,
            'staticAccountResponses' => [['id' => 'wallet-1']],
        ], 200),
    ]);

    $response = AlatPay::staticWallet()->list();

    expect($response['totalCount'])->toBe(1);

    Http::assertSent(fn ($request) => str_contains($request->url(), 'PageNumber=1') && str_contains($request->url(), 'Limit=10'));
});

it('retrieves static wallet collection history', function () {
    Http::fake([
        '*/alatpay-wallet/api/v1/staticaccount/collectionhistory*' => Http::response([
            'staticAccountTransactionResponses' => [['amount' => 100]],
        ], 200),
    ]);

    $response = AlatPay::staticWallet()->history();

    expect($response['staticAccountTransactionResponses'])->toHaveCount(1);
});

it('retrieves static wallet details', function () {
    Http::fake([
        '*/alatpay-wallet/api/v1/staticaccount/staticAccountId*' => Http::response([
            'accountNumber' => '043233321',
            'bankName' => 'Wema Bank',
        ], 200),
    ]);

    $response = AlatPay::staticWallet()->details('wallet-1');

    expect($response['bankName'])->toBe('Wema Bank');
});
