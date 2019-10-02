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
     * Url to call
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
     * MandateRequest Status
     *
     * @var int
     */
    public $status;

    /**
     * Email address
     *
     * @var string
     */
    public $email;

    /**
     * UMR
     *
     * @var string
     */
    public $UMR;
}
