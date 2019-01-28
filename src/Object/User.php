<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Object;

class User extends AbstractHydratable
{
    /**
     * S-money user ID
     *
     * @var int
     */
    public $id;

    /**
     * User identifier in the third-party application
     *
     * @var string
     */
    public $appUserId;

    // Individual client
    const TYPE_INDIVIDUAL_CLIENT = 1;

    // Professional client
    const TYPE_PROFESSIONAL_CLIENT = 2;

    /**
     * User type
     *
     * @var int
     */
    public $type;

    /**
     * User’s profile information
     *
     * @var UserProfile
     */
    public $profile;

    /**
     * Cumulative amount of all user sub-accounts (in cents)
     *
     * @var int
     */
    public $amount;

    const STATUS_NOT_CONFIRMED = 0;
    const STATUS_OK = 1;
    const STATUS_FROZEN = 2;
    const STATUS_ON_THE_FLY = 3;
    const STATUS_BEING_CLOSED = 4;
    const STATUS_CLOSED = 5;
    const STATUS_WAITING_FOR_KYC = 6;
    const STATUS_BLOCKED = 7;

    /**
     * User status
     *
     * @var int
     */
    public $status;

    /**
     * If the created user is a company, then it contains information of the company.
     *
     * @var Company
     */
    public $company;
}
