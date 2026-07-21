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

namespace RoyalBcode\AlatPay;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Client\PendingRequest;
use RoyalBcode\AlatPay\Exceptions\AlatPayException;
use RoyalBcode\AlatPay\Services\UssdService;
use RoyalBcode\AlatPay\Services\SettlementService;
use RoyalBcode\AlatPay\Services\TransactionService;
use RoyalBcode\AlatPay\Services\Paymentlinkservice;
use RoyalBcode\AlatPay\Services\StaticWalletService;
use RoyalBcode\AlatPay\Services\BankTransferService;
use RoyalBcode\AlatPay\Services\AccountNumberService;

class AlatPay
{
    protected string $secretKey;

    protected string $publicKey;

    protected string $businessId;

    protected string $baseUrl;

    protected int $timeout;

    protected bool $passCharge;

    public function __construct(array $config = [])
    {
        $this->secretKey = (string) ($config['secret_key'] ?? '');
        $this->publicKey = (string) ($config['public_key'] ?? '');
        $this->businessId = (string) ($config['business_id'] ?? '');
        $this->baseUrl = rtrim((string) ($config['base_url'] ?? 'https://apibox.alatpay.ng'), '/');
        $this->timeout = (int) ($config['timeout'] ?? 30);
        $this->passCharge = (bool) ($config['pass_charge'] ?? false);
    }

    public function getSecretKey(): string
    {
        return $this->secretKey;
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function getBusinessId(): string
    {
        return $this->businessId;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * The default Fee Bearer setting: whether the transaction fee is passed
     * on to the customer (true) or absorbed by the merchant (false).
     *
     * Configurable via the ALATPAY_PASS_CHARGE env variable. Individual
     * requests can still override this per call via the `passCharge` key.
     */
    public function getPassCharge(): bool
    {
        return $this->passCharge;
    }
    
    /**
     * Build a pre-authenticated HTTP client pointed at the ALATPay API.
     */
    public function http(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Ocp-Apim-Subscription-Key' => $this->secretKey,
            ])
            ->timeout($this->timeout);
    }

    /**
     * Normalize a response into an array, or throw on failure.
     *
     * @throws AlatPayException
     */
    public function handleResponse(Response $response): array
    {
        $payload = $response->json() ?? [];

        if ($response->failed() || ($payload['status'] ?? true) === false) {
            throw new AlatPayException(
                $payload['message'] ?? 'The ALATPay API request failed.',
                $response->status(),
                $payload
            );
        }

        return $payload;
    }

    public function bankTransfer(): BankTransferService
    {
        return new BankTransferService($this);
    }

    public function ussd(): UssdService
    {
        return new UssdService($this);
    }

    public function accountNumber(): AccountNumberService
    {
        return new AccountNumberService($this);
    }

    public function staticWallet(): StaticWalletService
    {
        return new StaticWalletService($this);
    }

    public function transactions(): TransactionService
    {
        return new TransactionService($this);
    }

    public function settlements(): SettlementService
    {
        return new SettlementService($this);
    }

    public function paymentLinks(): PaymentLinkService
    {
        return new PaymentLinkService($this);
    }
}
