<?php

declare(strict_types=1);

namespace AssoConnect\SMoney;

class Callbacks
{
    const TYPE_MONEY_IN_WEB			    = 1;
    const TYPE_KYC					    = 4;
    const TYPE_CARD					    = 5;
    const TYPE_MONEY_IN_SEPA		    = 7;
    const TYPE_MONEY_IN_TRANSFER	    = 8;
    const TYPE_MANDATE				    = 9;
    const TYPE_KYC_BANK_ACCOUNT		    = 10;
    const TYPE_REFUND_TRANSFER		    = 11;
    const TYPE_MONEY_IN_CHECKNOTE	    = 12;
    const TYPE_SEPA_OUTSTANDING_DEBT    = 14;
}
