<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Object;

/**
 * Payment between two S-Money users or sub-accounts
 */
class InternalPayment extends AbstractPayment
{
    public const STATUS_WAITING = 0;

    public const STATUS_REFUNDED = 1;

    public const STATUS_EXPIRED = 2;

    public const STATUS_COMPLETED = 3;

    public const STATUS_ERROR = 4;

    /**
     * Sender's SubAccount
     */
    public SubAccount $sender;

    /**
     * Recipient's SubAccount
     */
    public SubAccount $beneficiary;
}
