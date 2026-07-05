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

class StaticWalletService extends AbstractService
{
    /**
     * Personal wallet linked to the account owner's BVN.
     */
    public const WALLET_TYPE_INDIVIDUAL = 1;

    /**
     * Business wallet linked to shareholder BVNs, supports multiple accounts.
     */
    public const WALLET_TYPE_COLLECTION = 2;

    /**
     * Create a new static wallet. Triggers an OTP to the BVN-linked phone number.
     *
     * @param  array{staticWalletType: int, bvn: string, email?: string, businessId?: string}  $data
     *
     * @throws AlatPayException
     */
    public function create(array $data): array
    {
        $data['businessId'] = $data['businessId'] ?? $this->client->getBusinessId();

        $response = $this->client->http()->post(
            '/alatpay-wallet/api/v1/staticaccount',
            $data
        );

        return $this->client->handleResponse($response);
    }

    /**
     * Confirm the OTP and finalize static wallet creation.
     *
     * @param  array{staticWalletId: string, otp: string, trackingId: string, businessId?: string}  $data
     *
     * @throws AlatPayException
     */
    public function validateAndCreate(array $data): array
    {
        $data['businessId'] = $data['businessId'] ?? $this->client->getBusinessId();

        $response = $this->client->http()->post(
            '/alatpay-wallet/api/v1/staticaccount/validateAndCreate',
            $data
        );

        return $this->client->handleResponse($response);
    }

    /**
     * List all static wallet accounts belonging to the business.
     *
     * @param  array{page?: int, limit?: int, status?: int, businessId?: string}  $params
     *
     * @throws AlatPayException
     */
    public function list(array $params = []): array
    {
        $query = [
            'BusinessId' => $params['businessId'] ?? $this->client->getBusinessId(),
            'PageNumber' => $params['page'] ?? 1,
            'Limit' => $params['limit'] ?? 10,
        ];

        if (isset($params['status'])) {
            $query['Status'] = $params['status'];
        }

        $response = $this->client->http()->get(
            '/alatpay-wallet/api/v1/staticaccount',
            $query
        );

        return $this->client->handleResponse($response);
    }

    /**
     * Retrieve the transaction/collection history for static wallets.
     *
     * @param  array{page?: int, limit?: int, status?: int, businessId?: string}  $params
     *
     * @throws AlatPayException
     */
    public function history(array $params = []): array
    {
        $query = [
            'BusinessId' => $params['businessId'] ?? $this->client->getBusinessId(),
            'PageNumber' => $params['page'] ?? 1,
            'Limit' => $params['limit'] ?? 10,
        ];

        if (isset($params['status'])) {
            $query['Status'] = $params['status'];
        }

        $response = $this->client->http()->get(
            '/alatpay-wallet/api/v1/staticaccount/collectionhistory',
            $query
        );

        return $this->client->handleResponse($response);
    }

    /**
     * Retrieve detailed information about a single static wallet.
     *
     * @throws AlatPayException
     */
    public function details(string $staticAccountId): array
    {
        $response = $this->client->http()->get(
            '/alatpay-wallet/api/v1/staticaccount/staticAccountId',
            ['StaticAccountId' => $staticAccountId]
        );

        return $this->client->handleResponse($response);
    }
}
