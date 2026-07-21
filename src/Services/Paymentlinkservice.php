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

class PaymentLinkService extends AbstractService
{
    /**
     * NGN currency code expected by the Payment Link endpoint.
     */
    public const CURRENCY_NGN = 2;

    /**
     * Create a hosted payment link that can be shared with a customer.
     *
     * @param  array{email: string, redirectUrl: string, amount: float|int, currency?: int, passCharge?: bool}  $data
     *
     * @throws AlatPayException
     */
    public function create(array $data): array
    {
        $data['currency'] = $data['currency'] ?? self::CURRENCY_NGN;
        $data['passCharge'] = $data['passCharge'] ?? $this->client->getPassCharge();

        $response = $this->client->http()->post(
            '/merchant-onboarding/api/v1/payment/initialize',
            $data
        );

        return $this->client->handleResponse($response);
    }

    /**
     * Check the status of a payment made against a payment link, using the
     * paymentReference returned when the link was created.
     *
     * @throws AlatPayException
     */
    public function status(string $reference): array
    {
        $response = $this->client->http()->get(
            "/merchant-onboarding/api/v1/payment/status/{$reference}"
        );

        return $this->client->handleResponse($response);
    }
}