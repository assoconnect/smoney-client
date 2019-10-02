<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Object;

/**
 * Account maintained by a real-world bank for a User
 */
class BankAccount extends AbstractHydratable
{
    /**
     * S-Money generated BankAccount's ID
     *
     * @var int
     */
    public $id;

    /**
     * BankAccount's name for display purpose only
     *
     * @var string
     */
    public $displayName;

    /**
     * The BankAccount successfully went through the KYC process
     */
    public const STATUS_VALIDATED = 1;

    /**
     * The BankAccount is currently in the KYC process
     */
    public const STATUS_PENDING_VALIDATION = 2;

    /**
     * The KYC process failed for this BankAccount
     */
    public const STATUS_DENIED = 3;

    /**
     * BankAccount's status
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
