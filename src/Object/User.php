<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Object;

/**
 * A User is a person or a corporation owning funds on S-Money
 */
class User extends AbstractHydratable
{
    /**
     * S-Money generated User's ID
     *
     * @var int
     */
    public $id;

    /**
     * Third-party generated User's ID
     *
     * @var string
     */
    public $appUserId;

    /**
     * The User is a person
     */
    public const TYPE_INDIVIDUAL_CLIENT = 1;

    /**
     * The User is a corporation
     */
    public const TYPE_PROFESSIONAL_CLIENT = 2;

    /**
     * User's type
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
     * Cumulative balance of all User's sub-accounts (in cents)
     *
     * @var int
     */
    public $amount;

    public const STATUS_NOT_CONFIRMED = 0;

    /**
     * User is active
     */
    public const STATUS_OK = 1;
    public const STATUS_FROZEN = 2;
    public const STATUS_ON_THE_FLY = 3;
    public const STATUS_BEING_CLOSED = 4;
    public const STATUS_CLOSED = 5;
    public const STATUS_WAITING_FOR_KYC = 6;
    public const STATUS_BLOCKED = 7;

    /**
     * User's status
     *
     * @var int
     */
    public $status;

    /**
     * If the User is a company, then it contains information of the company.
     *
     * @var Company
     */
    public $company;
}
