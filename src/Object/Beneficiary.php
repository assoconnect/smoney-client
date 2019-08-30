<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Object;

/**
 * A Beneficiary is the recipient of a financial operation.
 * Required when this recipient is a given Account of a User.
 * If not used, then the recipient is the default Account of the User.
 */
class Beneficiary extends AbstractHydratable
{
    /**
     *  S-Money generated Beneficiary's ID
     *
     * @var int
     */
    public $id;

    /**
     *  Beneficiary's name for display purpose
     *
     * @var string
     */
    public $displayName;

    /**
     * Third-party generated Beneficiary's AppAccountId
     *
     * @var string
     */
    public $appAccountId;
}
