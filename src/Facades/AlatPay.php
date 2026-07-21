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

namespace RoyalBcode\AlatPay\Facades;

use Illuminate\Support\Facades\Facade;
use RoyalBcode\AlatPay\Services\AccountNumberService;
use RoyalBcode\AlatPay\Services\BankTransferService;
use RoyalBcode\AlatPay\Services\PaymentLinkService;
use RoyalBcode\AlatPay\Services\SettlementService;
use RoyalBcode\AlatPay\Services\StaticWalletService;
use RoyalBcode\AlatPay\Services\TransactionService;
use RoyalBcode\AlatPay\Services\UssdService;

/**
 * @method static BankTransferService bankTransfer()
 * @method static UssdService ussd()
 * @method static AccountNumberService accountNumber()
 * @method static StaticWalletService staticWallet()
 * @method static TransactionService transactions()
 * @method static SettlementService settlements()
 * @method static PaymentLinkService paymentLink()
 * @method static string getBusinessId()
 * @method static string getPublicKey()
 * @method static string getBaseUrl()
 * @method static bool getPassCharge()
 *
 * @see \RoyalBcode\AlatPay\AlatPay
 */
class AlatPay extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \RoyalBcode\AlatPay\AlatPay::class;
    }
}