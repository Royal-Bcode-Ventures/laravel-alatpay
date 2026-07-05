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

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RoyalBcode\AlatPay\Exceptions\AlatPayException;
use RoyalBcode\AlatPay\Services\AccountNumberService;
use RoyalBcode\AlatPay\Services\BankTransferService;
use RoyalBcode\AlatPay\Services\SettlementService;
use RoyalBcode\AlatPay\Services\StaticWalletService;
use RoyalBcode\AlatPay\Services\TransactionService;
use RoyalBcode\AlatPay\Services\UssdService;

class AlatPay
{
    protected string $secretKey;

    protected string $publicKey;

    protected string $businessId;

    protected string $baseUrl;

    protected int $timeout;

    public function __construct(array $config = [])
    {
        $this->secretKey = (string) ($config['secret_key'] ?? '');
        $this->publicKey = (string) ($config['public_key'] ?? '');
        $this->businessId = (string) ($config['business_id'] ?? '');
        $this->baseUrl = rtrim((string) ($config['base_url'] ?? 'https://apibox.alatpay.ng'), '/');
        $this->timeout = (int) ($config['timeout'] ?? 30);
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
}
