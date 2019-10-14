<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Object;

class Mandate extends AbstractHydratable
{

    /**
     * Mandate ID
     *
     * @var int
     */
    public $id;

    /**
     * S-Money Mandate is pending
     */
    public const STATUS_PENDING = 0;

    /**
     *  S-Money Mandate has been validated
     */
    public const STATUS_VALIDATED = 1;

    /**
     * S-Money Mandate has been dismissed
     */
    public const STATUS_DISMISSED = 2;

    /**
     * S-Money Mandate has failed
     */
    public const STATUS_FAILURE = 3;

    /**
     * Mandate Status
     *
     * Values :
     * 0 = Pending
     * 1 = Validated
     * 2 = Dismissed
     * 3 = Failure
     * @var int
     */
    public $status;


    /**
     * BankAccount information
     *
     * @var BankAccount
     */
    public $bankAccount;

    /**
     * Mandate's date
     *
     * @var \DateTime
     */
    public $date;

    /**
     * Mandate Single Reference
     *
     * @var string
     */
    public $UMR;

    /**
     * Mandate demands request details
     *
     * @var array
     */
    public $mandateDemands;
}
