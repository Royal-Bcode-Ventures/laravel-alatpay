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

class SettlementService extends AbstractService
{
    /**
     * Retrieve a paginated, filterable list of settlement/payment records
     * for the business.
     *
     * @param  array{businessId?: string, merchantId?: string, status?: string, startAt?: string, endAt?: string}  $filters
     *
     * @throws AlatPayException
     */
    public function all(array $filters = []): array
    {
        $query = [
            'businessId' => $filters['businessId'] ?? $this->client->getBusinessId(),
        ];

        $optional = [
            'merchantId' => 'merchantId',
            'status' => 'status',
            'startAt' => 'startAt',
            'endAt' => 'endAt',
        ];

        foreach ($optional as $filterKey => $queryKey) {
            if (isset($filters[$filterKey])) {
                $query[$queryKey] = $filters[$filterKey];
            }
        }

        $response = $this->client->http()->get(
            '/payment-settlement/api/v1/settlements',
            $query
        );

        return $this->client->handleResponse($response);
    }
}
