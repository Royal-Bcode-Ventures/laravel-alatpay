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

class BankTransferService extends AbstractService
{
    /**
     * Generate a virtual account number for a customer to pay into.
     *
     * @param  array{amount: float|int, currency?: string, orderId: string, description?: string, customer: array}  $data
     *
     * @throws AlatPayException
     */
    public function generateVirtualAccount(array $data): array
    {
        $data['businessId'] = $data['businessId'] ?? $this->client->getBusinessId();
        $data['currency'] = $data['currency'] ?? 'NGN';

        $response = $this->client->http()->post(
            '/bank-transfer/api/v1/bankTransfer/virtualAccount',
            $data
        );

        return $this->client->handleResponse($response);
    }

    /**
     * Check the status of a bank transfer transaction.
     *
     * @throws AlatPayException
     */
    public function confirmTransaction(string $transactionId): array
    {
        $response = $this->client->http()->get(
            "/bank-transfer/api/v1/bankTransfer/transactions/{$transactionId}"
        );

        return $this->client->handleResponse($response);
    }
}
