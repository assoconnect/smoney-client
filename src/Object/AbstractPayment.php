<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Object;

abstract class AbstractPayment extends AbstractHydratable
{
    /**
     * S-Money generated Payment's ID
     *
     * @var int
     */
    public $id;

    /**
     * Third-party generated Payment's ID
     *
     * @var string
     */
    public $orderId;

    /**
     * Payment's status
     *
     * @var int
     */
    public $status;

    /**
     * Amount in cents
     *
     * @var int
     */
    public $amount;

    /**
     * Payment's date
     *
     * @var \DateTime
     */
    public $paymentDate;
}
