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

namespace RoyalBcode\AlatPay\Exceptions;

use Exception;

class AlatPayException extends Exception
{
    /** Invalid request: missing parameters or malformed JSON payload. */
    public const BAD_REQUEST = 400;

    /** Access denied: the Ocp-Apim-Subscription-Key header is missing or invalid. */
    public const UNAUTHORIZED = 401;

    /** ALATPay was unable to generate a virtual account; contact ALATPay support. */
    public const EXPECTATION_FAILED = 417;

    /** A required field is missing from the request payload. */
    public const UNPROCESSABLE_ENTITY = 422;

    /**
     * The full decoded JSON body returned by ALATPay, when available.
     */
    protected array $context;

    public function __construct(string $message, protected int $statusCode = 0, array $context = [])
    {
        parent::__construct($message);

        $this->context = $context;
    }

    /**
     * HTTP status code returned by the ALATPay API.
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Raw response payload returned by the ALATPay API, if any.
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * The request was malformed or missing required parameters (HTTP 400).
     */
    public function isBadRequest(): bool
    {
        return $this->statusCode === self::BAD_REQUEST;
    }

    /**
     * The Ocp-Apim-Subscription-Key was missing or invalid (HTTP 401).
     */
    public function isUnauthorized(): bool
    {
        return $this->statusCode === self::UNAUTHORIZED;
    }

    /**
     * ALATPay could not generate a virtual account (HTTP 417).
     */
    public function isVirtualAccountGenerationFailed(): bool
    {
        return $this->statusCode === self::EXPECTATION_FAILED;
    }

    /**
     * A required field was missing from the payload (HTTP 422).
     */
    public function isValidationError(): bool
    {
        return $this->statusCode === self::UNPROCESSABLE_ENTITY;
    }

    /**
     * Something went wrong on ALATPay's end (HTTP 5xx).
     */
    public function isServerError(): bool
    {
        return $this->statusCode >= 500;
    }
}
