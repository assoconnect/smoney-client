<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Object;

/**
 * SubPayment of a CardPayment
 * Subpayments exist when a CardPayment has multiple recipients
 */
class CardSubPayment extends AbstractPayment
{
    /**
     * Beneficiary
     *
     * @var array
     */
    public $beneficiary;

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
