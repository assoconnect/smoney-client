<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Object;

class Beneficiary extends AbstractHydratable
{
    /**
     *  MoneyIn beneficiary id
     *
     * @var int
     */
    public $id;

    /**
     *  MoneyIn beneficiary displayName
     *
     * @var string
     */
    public $displayName;

    /**
     * MoneyIn beneficiary AppaccountId
     *
     * @var int
     */
    public $appAccountId;
}
