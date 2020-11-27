<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Parser;

use AssoConnect\SMoney\Object\Payout;

class PayoutParser
{
    public function parse(array $data): Payout
    {
        $properties = [
            'id'     => $data['Id'],
            'orderId' => $orderId,
            'amount' => $amount,
        ];

        return new Payout($properties);
    }
}
