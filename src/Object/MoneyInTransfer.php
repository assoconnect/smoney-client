<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Object;

/**
 * A MoneyInTransfer is an incoming payment from a bank account to an S-Money User
 * S-Money exposes a common IBAN to receive funds and uses the label (named Reference) of the transfer to find the right
 * User to deposit funds to.
 */
class MoneyInTransfer extends AbstractHydratable
{
    /**
     * S-Money generated MoneyInTransfer's ID
     *
     * @var int
     */
    public $id;

    /**
     * MoneyInTransfer's amount in cents
     *
     * @var int
     */
    public $amount;

    /**
     * Successful payment
     */
    public const STATUS_OK = 1;

    /**
     * MoneyInTransfer's status
     *
     * @var int
     */
    public $status;

    /**
     * MoneyInTransfer's Beneficiary
     *
     * @var Beneficiary
     */
    public $beneficiary;
}
