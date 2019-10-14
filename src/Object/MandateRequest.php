<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Object;

class MandateRequest extends AbstractHydratable
{

    /**
     * MandateRequest ID
     *
     * @var int
     */
    public $id;

    /**
     * S-Money Mandate Request is pending
     */
    public const STATUS_PENDING = 0;

    /**
     *  S-Money Mandate Request has been validated
     */
    public const STATUS_VALIDATED = 1;

    /**
     * S-Money Mandate Request has been dismissed
     */
    public const STATUS_DISMISSED = 2;

    /**
     * S-Money Mandate Request has failed
     */
    public const STATUS_FAILURE = 3;

    /**
     * MandateRequest Status
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
     * Url to call in order to electronically sign the mandate
     *
     * @var string
     */
    public $href;

    /**
     * BankAccount information
     *
     * @var BankAccount
     */
    public $bankAccount;

    /**
     * Request's date
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
}
