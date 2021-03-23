<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Object;

class CardRefund extends AbstractHydratable
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
     * Refund's status
     *
     * @var int
     */
    public $status;
}
