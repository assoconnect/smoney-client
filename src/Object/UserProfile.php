<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Object;

/**
 * The UserProfile holds the information about:
 * - the person in case the User is an individual
 * - the person in charge of the corporation in case the User is a company
 */
class UserProfile extends AbstractHydratable
{
    /**
     * The person is male
     */
    const CIVILITY_MR = 0;

    /**
     * The person is female
     */
    const CIVILITY_MRS_MISS = 1;

    /**
     * Civility
     *
     * @var int
     */
    public $civility;

    /**
     * User's first name
     *
     * @var string
     */
    public $firstname;

    /**
     * User's last name
     *
     * @var string
     */
    public $lastname;

    /**
     * Date of birth
     *
     * @var \DateTime
     */
    public $birthdate;

    /**
     * Address
     *
     * @var Address
     */
    public $address;

    /**
     * Email address
     *
     * @var string
     */
    public $email;
}
