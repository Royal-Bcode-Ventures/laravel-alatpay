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

namespace RoyalBcode\AlatPay\Services;

use RoyalBcode\AlatPay\Exceptions\AlatPayException;

class UssdService extends AbstractService
{
    /**
     * Initiate a Pay-with-Phone (USSD) direct debit request.
     *
     * @param  array{amount: float|int, currency?: string, customer: array, phonenumber: string, businessId?: string}  $data
     *
     * @throws AlatPayException
     */
    public function initiate(array $data): array
    {
        $data['businessId'] = $data['businessId'] ?? $this->client->getBusinessId();
        $data['currency'] = $data['currency'] ?? 'NGN';

        $response = $this->client->http()->post(
            '/alatpay-phone-number/api/v1/phone-number-payment/initialize',
            $data
        );

        return $this->client->handleResponse($response);
    }

    /**
     * Authorise/complete a previously initiated USSD debit request.
     *
     * @param  array{phonenumber: string, amount: float|int, businessid?: string, currency?: string, transactionId: string}  $data
     *
     * @throws AlatPayException
     */
    public function validateAndPay(array $data): array
    {
        $data['businessid'] = $data['businessid'] ?? $this->client->getBusinessId();
        $data['currency'] = $data['currency'] ?? 'NGN';

        $response = $this->client->http()->post(
            '/alatpay-phone-number/api/v1/phone-number-payment/complete-phonenumber-payment',
            $data
        );

        return $this->client->handleResponse($response);
    }
}
