<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Object;

/**
 * Online payment with a card on the S-Money hosted form.
 * This object can also describe a refund.
 */
class CardPayment extends AbstractHydratable
{
    /**
     * S-Money generated CardPayment's ID
     *
     * @var int
     */
    public $id;

    /**
     * Third-party generated CardPayment's ID
     *
     * @var string
     */
    public $orderId;

    /**
     * Card used by the end-user to pay
     *
     * @var iterable
     */
    public $card;

    /**
     * List of payments if there are multiple recipients
     *
     * @var CardSubPayment[]
     */
    public $subPayments = [];

    /**
     * True if the card holder is the same as the beneficiary
     *
     * @var bool
     */
    public $isMine;

    /**
     * URL where the card holder will be redirected to once he submits the payment form.
     *
     * @var string
     */
    public $urlReturn;

    /**
     * URL where S-Money server sends a notification request to the third party server.
     *
     * @var string
     */
    public $urlCallback;

    /**
     * The payment has been initiated but the payment form has not been submitted
     */
    public const STATUS_PENDING = 0;

    /**
     * The payment has been successfully completed
     */
    public const STATUS_SUCCESS = 1;

    /**
     * The payment has been refunded
     */
    public const STATUS_REFUNDED = 2;

    /**
     * The payment has failed
     */
    public const STATUS_FAILED = 3;

    /**
     * The payment has been canceled before completion
     */
    public const STATUS_CANCELED = 5;

    /**
     * CardPayment's status
     *
     * @var int
     */
    public $status;

    public const TYPE_PAYMENT = 0;
    public const TYPE_REFUND = 1;

    /**
     * Payment or refund
     *
     * @var int
     */
    public $type;

    /**
     * True to require the 3-D Secure additional security layer
     * If not provided, default setting of the third party account will be used.
     *
     * @var bool
     */
    public $require3DS;

    /**
     * Extra data about the payment like the bank response or final status of the 3-D secure process
     *
     * @var iterable
     */
    public $extraResults;

    /**
     * Error code for failed payment
     *
     * @var int
     */
    public $errorCode;
}
