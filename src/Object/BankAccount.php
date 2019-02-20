<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Object;

class BankAccount extends AbstractHydratable
{
    /**
     * BankAccount ID
     *
     * @var int
     */
    public $id;

    /**
     * BankAccount name
     *
     * @var string
     */
    public $displayName;

    const STATUS_VALIDATED = 1;

    const STATUS_PENDING_VALIDATION = 2;

    const STATUS_DENIED = 3;

    /**
     * BankAccount Status
     *
     * @var int
     */
    public $status;

    /**
     * BankAccount's BIC
     *
     * @var string
     */
    public $bic;

    /**
     * BankAccount's IBAN
     *
     * @var string
     */
    public $iban;
}
