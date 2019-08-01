<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Object;

class CardSubPayment extends AbstractHydratable
{
    /**
     * S-money ID
     *
     * @var int
     */
    public $id;

    /**
     * OrderId
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
     * Amount
     *
     * @var int
     */
    public $amount;

    /**
     * sub payment status
     *
     * @var int
     */
    public $status;
}
