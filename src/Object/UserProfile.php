<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Object;

class UserProfile extends User
{
    const CIVILITY_MR = 0;
    const CIVILITY_MRS_MISS = 1;

    /**
     * Civility
     *
     * @var int
     */
    public $civility;

    /**
     * User's firstname
     *
     * @var string
     */
    public $firstname;

    /**
     * User's surname
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
