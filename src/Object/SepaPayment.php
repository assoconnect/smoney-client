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
}
