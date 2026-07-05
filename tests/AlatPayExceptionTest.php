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

use RoyalBcode\AlatPay\Exceptions\AlatPayException;

it('exposes status code and raw context', function () {
    $exception = new AlatPayException('Something went wrong', 422, ['status' => false, 'message' => 'Something went wrong']);

    expect($exception->getMessage())->toBe('Something went wrong')
        ->and($exception->getStatusCode())->toBe(422)
        ->and($exception->getContext())->toBe(['status' => false, 'message' => 'Something went wrong']);
});

it('identifies each ALATPay documented error code correctly', function () {
    expect((new AlatPayException('x', 400))->isBadRequest())->toBeTrue()
        ->and((new AlatPayException('x', 401))->isUnauthorized())->toBeTrue()
        ->and((new AlatPayException('x', 417))->isVirtualAccountGenerationFailed())->toBeTrue()
        ->and((new AlatPayException('x', 422))->isValidationError())->toBeTrue()
        ->and((new AlatPayException('x', 500))->isServerError())->toBeTrue()
        ->and((new AlatPayException('x', 503))->isServerError())->toBeTrue()
        ->and((new AlatPayException('x', 200))->isServerError())->toBeFalse();
});
