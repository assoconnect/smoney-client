<?php

declare(strict_types=1);

namespace AssoConnect\SMoney;

class Callbacks
{
    public const TYPE_MONEY_IN_WEB          = 1;
    public const TYPE_KYC                   = 4;
    public const TYPE_CARD                  = 5;
    public const TYPE_MONEY_IN_SEPA         = 7;
    public const TYPE_MONEY_IN_TRANSFER     = 8;
    public const TYPE_MANDATE               = 9;
    public const TYPE_KYC_BANK_ACCOUNT      = 10;
    public const TYPE_REFUND_TRANSFER       = 11;
    public const TYPE_MONEY_IN_CHECKNOTE    = 12;
    public const TYPE_SEPA_OUTSTANDING_DEBT = 14;
}
