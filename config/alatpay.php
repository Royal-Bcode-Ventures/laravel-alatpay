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

return [

    /*
    |--------------------------------------------------------------------------
    | ALATPay Secret Key
    |--------------------------------------------------------------------------
    |
    | Used server-side to authenticate every API request via the
    | "Ocp-Apim-Subscription-Key" header. Never expose this key
    | on the frontend.
    |
    */
    'secret_key' => env('ALATPAY_SECRET_KEY'),

    /*
    |--------------------------------------------------------------------------
    | ALATPay Public Key
    |--------------------------------------------------------------------------
    |
    | Safe to expose on the frontend. Used when integrating the
    | ALATPay web/checkout plugin client-side.
    |
    */
    'public_key' => env('ALATPAY_PUBLIC_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Business ID
    |--------------------------------------------------------------------------
    |
    | Your ALATPay merchant business identifier, found on your
    | ALATPay dashboard under Settings > Business.
    |
    */
    'business_id' => env('ALATPAY_BUSINESS_ID'),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | The ALATPay API base endpoint. Override if ALATPay ever provides
    | a distinct sandbox/staging host.
    |
    */
    'base_url' => env('ALATPAY_BASE_URL', 'https://apibox.alatpay.ng'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | Number of seconds to wait for a response before the HTTP client
    | throws a timeout exception.
    |
    */
    'timeout' => env('ALATPAY_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Fee Bearer (Pass Charge)
    |--------------------------------------------------------------------------
    |
    | Controls who absorbs the transaction processing fee by default.
    |
    | - false (default): ALATPay deducts the fee from your settlement.
    | - true: the fee is added on top, so the customer pays principal + fee.
    |
    | This is used as the default `passCharge` value for requests that
    | support it (Bank Transfer virtual accounts, Payment Links). You can
    | still override it per request by passing `passCharge` explicitly.
    |
    */
    'pass_charge' => env('ALATPAY_PASS_CHARGE', false),

];
