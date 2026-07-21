# Changelog

All notable changes to `royalbcode/laravel-alatpay` will be documented in this file.

## [1.0.1] - 2026-07-21

### Added
- Payment Links: create a hosted, shareable checkout link (`AlatPay::paymentLink()->create()`) and check its status by reference (`AlatPay::paymentLink()->status()`)
- Fee Bearer / Pass Charge support: `ALATPAY_PASS_CHARGE` env variable sets a global default for whether ALATPay's transaction fee is absorbed by the merchant or passed on to the customer; overridable per request via the `passCharge` key on Bank Transfer virtual accounts and Payment Links
- `AlatPay::getPassCharge()` accessor on the client

### Tests
- Added `PaymentLinkServiceTest`
- Added Fee Bearer default/override coverage to `BankTransferServiceTest`

## [1.0.0] - 2026-07-05

### Added
- Bank Transfer: generate virtual account, confirm transaction status
- USSD (Pay with Phone): initiate and validate/complete payment
- Pay with Account Number: send OTP, validate and pay (Wema Bank accounts)
- Static Wallets: create/validate (Individual & Collection), list, collection history, wallet details
- Transaction Monitoring: list/filter transactions, fetch a single transaction
- Settlements: list/filter settlement records
- `AlatPay` facade and container binding
- Typed `AlatPayException` with status-code helper methods matching ALATPay's documented error codes (400, 401, 417, 422, 5xx)
- Full Pest test suite with mocked HTTP responses
