<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Object;

/**
 * KYC stands for Know Your Customer and is the process of a business verifying the identity of its clients.
 * This object is a KYC submission to the S-Money back-office service
 */
class KYC extends User
{
    /**
     * S-Money generated KYC request's ID
     *
     * @var int
     */
    public $id;

    /**
     * Date and time of the KYC request's submission
     *
     * @var \DateTime
     */
    public $requestDate;

    /**
     * Reason in case the KYC request has been refused
     *
     * @var string
     */
    public $reason;

    /**
     * The KYC request has not been submitted
     */
    public const STATUS_INCOMPLETE = 0;

    /**
     * The KYC request has been submitted to S-Money and is pending review
     */
    public const STATUS_PENDING = 1;

    /**
     * S-Money has rejected the KYC request
     */
    public const STATUS_DENIED = 2;

    /**
     * S-Money has accepted the KYC request
     */
    public const STATUS_OK = 3;

    /**
     * KYC status
     *
     * @var int
     */
    public $status;
}
