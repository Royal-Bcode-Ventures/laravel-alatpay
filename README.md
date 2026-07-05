# Laravel ALATPay SDK

[![Latest Version](https://img.shields.io/badge/version-1.0.0-blue)](https://github.com/Royal-Bcode-Ventures/laravel-alatpay)
[![Tests](https://github.com/Royal-Bcode-Ventures/laravel-alatpay/actions/workflows/tests.yml/badge.svg)](https://github.com/Royal-Bcode-Ventures/laravel-alatpay/actions/workflows/tests.yml)
[![License: MIT](https://img.shields.io/badge/license-MIT-green)](LICENSE.md)
[![PHP Version](https://img.shields.io/badge/php-%5E8.1-777bb4)](composer.json)

An independent, open-source Laravel SDK for integrating **ALATPay** (a payment product by Wema Bank) into your Laravel application — virtual account bank transfers, USSD (Pay-with-Phone), Pay-with-Account-Number direct debit, and Static Wallets.

> **Disclaimer:** This package is an independent, community-built SDK. It is **not affiliated with, endorsed by, or maintained by Wema Bank PLC or ALATPay**. It simply wraps their publicly documented HTTP API for convenient use inside Laravel applications.

---

## Features

- 🏦 **Bank Transfer** — generate one-time virtual accounts and confirm transaction status
- 📱 **USSD (Pay with Phone)** — initiate and complete phone-based direct debits
- 💳 **Pay with Account Number** — OTP-based direct debit from a Wema Bank account
- 🗂 **Static Wallets** — create and manage Individual & Collection wallets, list accounts, and pull collection history
- 📊 **Transaction Monitoring** — list/filter every transaction across all payment channels, or fetch one by ID
- 💰 **Settlements** — retrieve and filter settlement/payout records for your business
- ⚡️ Laravel auto-discovery, Facade, and a clean, chainable service API
- 🧰 Built on Laravel's native `Http` client — no extra HTTP dependencies
- ✅ Typed exceptions (`AlatPayException`) with status code, raw API context, and helper methods matching ALATPay's documented error codes
- 🧪 Fully tested with Pest + Orchestra Testbench

---

## Installation

Install via Composer:

```bash
composer require royalbcode/laravel-alatpay
```

The package auto-registers its service provider and `AlatPay` facade. Publish the config file:

```bash
php artisan vendor:publish --tag=alatpay-config
```

Add your credentials to `.env`:

```env
ALATPAY_SECRET_KEY=your-secret-key
ALATPAY_PUBLIC_KEY=your-public-key
ALATPAY_BUSINESS_ID=your-business-id
ALATPAY_BASE_URL=https://apibox.alatpay.ng
ALATPAY_TIMEOUT=30
```

You can find your keys and Business ID on your ALATPay dashboard under **Settings → Business** (requires your ALATPay PIN).

---

## Usage

You can resolve the client via the `AlatPay` facade, dependency injection, or the `alatpay` container binding.

### 1. Bank Transfer (Virtual Account)

```php
use RoyalBcode\AlatPay\Facades\AlatPay;

// Step 1: generate a virtual account for the customer to pay into
$virtualAccount = AlatPay::bankTransfer()->generateVirtualAccount([
    'amount' => 5000,
    'orderId' => 'ORDER-001',
    'description' => 'Order payment',
    'customer' => [
        'email' => 'johndoe@email.com',
        'phone' => '08000000001',
        'firstName' => 'John',
        'lastName' => 'Doe',
    ],
]);

$transactionId = $virtualAccount['data']['transactionId'];
$accountNumber = $virtualAccount['data']['virtualBankAccountNumber'];

// Step 2: poll/confirm the transaction status
$status = AlatPay::bankTransfer()->confirmTransaction($transactionId);
```

### 2. USSD (Pay with Phone)

```php
// Step 1: initiate
$initiated = AlatPay::ussd()->initiate([
    'amount' => 250,
    'customer' => [
        'email' => 'example@example.com',
        'phone' => '1234567890',
        'firstName' => 'John',
        'lastName' => 'Doe',
    ],
    'phonenumber' => '0987654321',
]);

$transactionId = $initiated['data']['transactionId'];

// Step 2: customer approves the prompt on their phone, then validate
$result = AlatPay::ussd()->validateAndPay([
    'phonenumber' => '0987654321',
    'amount' => 250,
    'transactionId' => $transactionId,
]);
```

### 3. Pay with Account Number

```php
// Step 1: send an OTP to the account owner's registered phone number
$otp = AlatPay::accountNumber()->sendOtp([
    'amount' => 1000,
    'customer' => [
        'email' => 'jane.joe@email.com',
        'phone' => '+2348000000001',
        'firstName' => 'Jane',
        'lastName' => 'Joe',
    ],
    'accountNumber' => '0123456789',
    // 'bankCode' => '035', // defaults to Wema Bank's code automatically
]);

$transactionId = $otp['data']['transactionId'];

// Step 2: validate the OTP entered by the customer
$result = AlatPay::accountNumber()->validateAndPay([
    'otp' => '332610',
    'transactionId' => $transactionId,
]);
```

### 4. Static Wallets

```php
use RoyalBcode\AlatPay\Services\StaticWalletService;

// Create an individual wallet (BVN-linked)
$wallet = AlatPay::staticWallet()->create([
    'staticWalletType' => StaticWalletService::WALLET_TYPE_INDIVIDUAL,
    'bvn' => '12345678901',
    'email' => 'owner@example.com', // optional
]);

// Validate the OTP sent to the BVN-linked phone number
$created = AlatPay::staticWallet()->validateAndCreate([
    'staticWalletId' => $wallet['id'],
    'otp' => '332610',
    'trackingId' => $wallet['otpTrackingID'],
]);

// List all static wallets for the business
$wallets = AlatPay::staticWallet()->list(['page' => 1, 'limit' => 10]);

// Pull collection/transaction history
$history = AlatPay::staticWallet()->history(['page' => 1, 'limit' => 10]);

// Get details for one wallet
$details = AlatPay::staticWallet()->details($wallet['id']);
```

### 5. Transaction Monitoring

```php
// List/filter all transactions across every payment channel
$transactions = AlatPay::transactions()->all([
    'page' => 1,
    'limit' => 20,
    'status' => 'completed',       // optional
    'paymentMethod' => 'BankTransfer', // optional
    'amount' => 5000,              // optional
    'startAt' => '2026-01-01',     // optional
    'endAt' => '2026-01-31',       // optional
]);

// Fetch a single transaction by its ID
$transaction = AlatPay::transactions()->find('transaction-id');
```

### 6. Settlements

```php
// List/filter settlement (payout) records for your business
$settlements = AlatPay::settlements()->all([
    'status' => 'settled',      // optional
    'startAt' => '2026-01-01',  // optional
    'endAt' => '2026-01-31',    // optional
]);
```

---

## Error Handling

Every service method throws `RoyalBcode\AlatPay\Exceptions\AlatPayException` when ALATPay returns a failure response (non-2xx status, or `"status": false` in the payload):

```php
use RoyalBcode\AlatPay\Exceptions\AlatPayException;

try {
    AlatPay::bankTransfer()->generateVirtualAccount([...]);
} catch (AlatPayException $e) {
    $e->getMessage();      // ALATPay's error message
    $e->getStatusCode();   // HTTP status code returned
    $e->getContext();      // Full decoded JSON response body

    // Convenience checks matching ALATPay's documented response codes
    $e->isBadRequest();                     // 400 - missing/invalid parameters
    $e->isUnauthorized();                   // 401 - invalid Ocp-Apim-Subscription-Key
    $e->isVirtualAccountGenerationFailed();  // 417 - contact ALATPay support
    $e->isValidationError();                // 422 - a required field is missing
    $e->isServerError();                    // 5xx - something went wrong on ALATPay's end
}
```

ALATPay's documented response codes:

| Code | Status | Meaning | What to do |
|------|--------|---------|------------|
| 200 / 201 | Success | Request succeeded | — |
| 400 | Bad Request | Invalid request | Check required parameters and JSON validity |
| 401 | Unauthorized | Invalid subscription key | Verify `ALATPAY_SECRET_KEY` |
| 417 | Expectation Failed | Virtual account generation failed | Contact ALATPay support |
| 422 | Unprocessable Entity | A required field is missing | Check `$e->getMessage()` for the missing field |
| 5xx | Server Error | Something went wrong on ALATPay's end | Retry later |

---

## Using Without the Facade

Resolve the client from the container if you prefer constructor injection:

```php
use RoyalBcode\AlatPay\AlatPay;

class CheckoutController
{
    public function __construct(private AlatPay $alatPay) {}

    public function store()
    {
        return $this->alatPay->bankTransfer()->generateVirtualAccount([...]);
    }
}
```

---

## Testing

```bash
composer install
vendor/bin/pest
```

All HTTP calls in the test suite are mocked with Laravel's `Http::fake()` — no live ALATPay credentials are required to run the tests.

---

## Roadmap

- [x] Transaction monitoring (list, filter, single lookup)
- [x] Settlement records (list, filter)
- [ ] Card payment channel
- [ ] Webhook signature verification helper
- [ ] Artisan command for quick credential/connectivity checks

---

## Contributing

Issues and pull requests are welcome at the [GitHub repository](https://github.com/Royal-Bcode-Ventures/laravel-alatpay).

## Security

If you discover a security issue, please email the maintainers directly rather than opening a public issue.

## License

Released under the [MIT License](LICENSE.md).

---

## Credits

Developed and maintained by **Royal Bcode Ventures Ltd**
Lead Developer: **Gift Balogun** — [royalbv.name.ng](https://royalbv.name.ng)
GitHub: [github.com/Royal-Bcode-Ventures](https://github.com/Royal-Bcode-Ventures)

This is an independent open-source project built for developer education and portfolio purposes. It is not affiliated with, endorsed by, or sponsored by Wema Bank PLC or ALATPay.
