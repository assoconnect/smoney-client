<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Parser;

use AssoConnect\SMoney\Object\Payout;

class PayoutParser
{
    public function parse(array $data): Payout
    {
        $properties = [
            'id'      => $data['Id'],
            'orderId' => $data['OrderId'],
            'amount'  => $data['Amount'],
            'status'  => $data['Status'],
        ];

        if (array_key_exists('ExecutedDate', $data) && $data['ExecutedDate'] !== null) {
            $properties['executedDate'] = new \DateTime($data['ExecutedDate']);
        }
        if (array_key_exists('OperationDate', $data) && $data['OperationDate'] !== null) {
            $properties['requestDate'] = new \DateTime($data['OperationDate']);
        }

        return new Payout($properties);
    }
}
