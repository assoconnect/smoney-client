<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Object;

class MoneyInTransfer extends AbstractHydratable
{
    /**
     * MoneyIn Smoney id
     *
     * @var int
     */
    public $id;

    /**
     * MoneyIn Smoney amount
     *
     * @var int
     */
    public $amount;

    const STATUS_INCOMPLETE = 0;
    const STATUS_PENDING = 1;
    const STATUS_DENIED = 2;
    const STATUS_OK = 3;

    /**
     * MoneyIn status
     *
     * @var int
     */
    public $status;

    /**
     * MoneyIn beneficiaryId
     *
     * @var int
     */
    public $beneficiaryId;

    /**
     * MoneyIn beneficiary AppaccountId
     *
     * @var int
     */
    public $beneficiaryIdAppAccountId;

    /**
     * MoneyIn beneficiary DisplayName
     *
     * @var string
     */
    public $beneficiaryDisplayName;
}
