<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Object;

class SubAccount extends AbstractHydratable
{
    /**
     * Account identifier
     *
     * @var int
     */
    public $id;

    /**
     * Account identifier in the third-party application
     *
     * @var string
     */
    public $appAccountId;

    /**
     * Display name
     *
     * @var string
     */
    public $displayName;

    /**
     * Account amount (in cents)
     *
     * @var int
     */
    public $amount = 0;
}
