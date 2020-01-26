<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Object;

/**
 * Payout via bank transfer
 */
class Payout extends AbstractHydratable
{
    /**
     * S-Money generated payout ID
     *
     * @var int
     */
    public $id;

    /**
     * Third-party generated payout ID
     *
     * @var string
     */
    public $orderId;

    /**
     * Payout's amount in cents
     *
     * @var int
     */
    public $amount;
}
