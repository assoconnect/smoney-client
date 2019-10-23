<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Object;

/**
 * Direct debit top-up via SEPA transfer.
 */
class SepaPayment extends AbstractHydratable
{
    /**
     * S-Money generated SEPA payment ID
     *
     * @var int
     */
    public $id;

    /**
     * Third-party generated SEPA payment ID
     *
     * @var string
     */
    public $orderId;

    /**
     * SEPA payment's amount in cents
     *
     * @var int
     */
    public $amount;

    /**
     * The SEPA payment has been submitted
     */
    public const STATUS_WAITING = 0;

    /**
     * The SEPA payment has been completed
     */
    public const STATUS_COMPLETED = 1;

    /**
     * The SEPA payment has been refunded
     */
    public const STATUS_REFUNDED = 2;

    /**
     * The SEPA payment has failed
     */
    public const STATUS_FAILED = 3;

    /**
     * The SEPA payment is waiting for validation
     */
    public const STATUS_WAITING_VALIDATION = 4;

    /**
     * The SEPA payment was canceled
     */
    public const STATUS_CANCELLED = 5;


    /**
     * SEPA payment's status
     *
     * @var int
     */
    public $status;

    /**
     * SEPA payment's date
     *
     * @var \DateTime
     */
    public $paymentDate;

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
