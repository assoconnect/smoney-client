<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Object;

class CardPayment extends AbstractHydratable
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
     * Card Sub Payments
     *
     * @var iterable
     */
    public $cardSubPayments;

    /**
     * is mine
     *
     * @var bool
     */
    public $isMine;

    /**
     * url return
     *
     * @var string
     */
    public $urlReturn;

    /**
     * url callback
     *
     * @var string
     */
    public $urlCallback;

    /**
     * Status
     *
     * @var int
     */
    public $status;

    /**
     * Type
     *
     * @var int
     */
    public $type;

    /**
     * Require 3DS
     *
     * @var bool
     */
    public $require3DS;
}
