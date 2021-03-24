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

    /**
     * Payout's status
     *
     * @var int
     */
    public $status;

    /**
     * Payout's request date
     *
     * @var \DateTime
     */
    public $requestDate;

    /**
     * Payout's execution date
     *
     * @var \DateTime
     */
    public $executedDate;
}
