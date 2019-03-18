<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Object;

class KYC extends User
{
    /**
     * KYC Smoney id
     *
     * @var int
     */
    public $id;

    /**
     * DateTime of the KYC request
     *
     * @var \DateTime
     */
    public $requestDate;

    /**
     * KYC refused reason
     *
     * @var string
     */
    public $reason;

    const STATUS_INCOMPLETE = 0;
    const STATUS_PENDING = 1;
    const STATUS_DENIED = 2;
    const STATUS_OK = 3;

    /**
     * KYC status
     *
     * @var int
     */
    public $status;
}
