<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Object;

/**
 * A SubAccount is associated to a User and holds its own operations and has its own balance
 */
class SubAccount extends AbstractHydratable
{
    /**
     * S-Money generated SubAccount's ID
     *
     * @var int
     */
    public $id;

    /**
     * Third-party generated SubAccount's ID
     *
     * @var string
     */
    public $appAccountId;

    /**
     * SubAccount's name for display purpose
     *
     * @var string
     */
    public $displayName;

    /**
     * SubAccount's balance in cents
     *
     * @var int
     */
    public $amount = 0;
}
