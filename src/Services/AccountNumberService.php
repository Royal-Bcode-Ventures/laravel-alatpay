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

class AccountNumberService extends AbstractService
{
    /**
     * WEMA Bank's institution code, required by the account-number-debit flow.
     */
    public const WEMA_BANK_CODE = '035';

    /**
     * Initiate a Pay-with-Account-Number debit and trigger an OTP to the customer.
     *
     * @param  array{amount: float|int, currency?: string, orderId?: string, description?: string, customer: array, accountNumber: string, bankCode?: string, businessId?: string}  $data
     *
     * @throws AlatPayException
     */
    public function sendOtp(array $data): array
    {
        $data['businessId'] = $data['businessId'] ?? $this->client->getBusinessId();
        $data['currency'] = $data['currency'] ?? 'NGN';
        $data['bankCode'] = $data['bankCode'] ?? self::WEMA_BANK_CODE;

        $response = $this->client->http()->post(
            '/alatpayaccountnumber/api/v1/accountNumber/sendOtp',
            $data
        );

        return $this->client->handleResponse($response);
    }

    /**
     * Validate the OTP sent to the customer and complete the debit.
     *
     * @param  array{otp: string, transactionId: string}  $data
     *
     * @throws AlatPayException
     */
    public function validateAndPay(array $data): array
    {
        $response = $this->client->http()->post(
            '/alatpayaccountnumber/api/v1/accountNumber/validateAndPay',
            $data
        );

        return $this->client->handleResponse($response);
    }
}
