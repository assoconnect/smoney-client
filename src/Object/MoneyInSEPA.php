<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Object;

/**
 * Direct debit top-up via SEPA transfer.
 */
class MoneyInSEPA extends AbstractHydratable
{
    /**
     * S-Money generated MoneyInSEPA's ID
     *
     * @var int
     */
    public $id;

    /**
     * Third-party generated MoneyInSEPA's ID
     *
     * @var string
     */
    public $orderId;

    /**
     * MoneyInSEPA's amount in cents
     *
     * @var int
     */
    public $amount;

    /**
     * MoneyInSEPA's status
     *
     * @var int
     */
    public $status;

    /**
     * MoneyInSEPA's payment date
     *
     * @var \DateTime
     */
    public $paymentDate;
}
