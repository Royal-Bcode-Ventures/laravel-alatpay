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

class TransactionService extends AbstractService
{
    /**
     * Retrieve a paginated, filterable list of all transactions performed
     * on behalf of the merchant, across every payment channel.
     *
     * @param  array{page?: int, limit?: int, merchantId?: string, businessId?: string, paymentMethod?: string, status?: string, amount?: float|int, startAt?: string, endAt?: string}  $filters
     *
     * @throws AlatPayException
     */
    public function all(array $filters = []): array
    {
        $query = [
            'Page' => $filters['page'] ?? 1,
            'BusinessId' => $filters['businessId'] ?? $this->client->getBusinessId(),
        ];

        $optional = [
            'limit' => 'Limit',
            'merchantId' => 'MerchantId',
            'paymentMethod' => 'PaymentMethod',
            'status' => 'Status',
            'amount' => 'Amount',
            'startAt' => 'StartAt',
            'endAt' => 'EndAt',
        ];

        foreach ($optional as $filterKey => $queryKey) {
            if (isset($filters[$filterKey])) {
                $query[$queryKey] = $filters[$filterKey];
            }
        }

        $response = $this->client->http()->get(
            '/alatpaytransaction/api/v1/transactions',
            $query
        );

        return $this->client->handleResponse($response);
    }

    /**
     * Retrieve the full details of a single transaction.
     *
     * @throws AlatPayException
     */
    public function find(string $transactionId): array
    {
        $response = $this->client->http()->get(
            "/alatpaytransaction/api/v1/transactions/{$transactionId}"
        );

        return $this->client->handleResponse($response);
    }
}
