<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Object;

/**
 * SubPayment of a CardPayment
 * Subpayments exist when a CardPayment has multiple recipients
 */
class CardSubPayment extends AbstractHydratable
{
    /**
     * S-money generated SubPayment's ID
     *
     * @var int
     */
    public $id;

    /**
     * Third-party generated SubPayment's ID
     *
     * @var string
     */
    public $orderId;

    /**
     * Beneficiary
     *
     * @var array
     */
    public $beneficiary;

    /**
     * Amount in cents
     *
     * @var int
     */
    public $amount;

    /**
     * Status
     *
     * @var int
     */
    public $status;

    /**
     * Card info
     *
     * @var iterable
     */
    public $card;

    /**
     * Extra data about the payment like the bank response or final status of the 3-D secure process
     *
     * @var iterable
     */
    public $extraResults;
}
