<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Parser;

use AssoConnect\SMoney\Object\SepaPayment;

class SepaPaymentParser
{
    public function parse(array $data): SepaPayment
    {
        $properties = [
            'id'     => $data['Id'],
            'orderId' => $data['OrderId'],
            'status' => $data['Status'],
            'amount' => $data['Amount'],
            'paymentDate' => new \DateTime($data['PaymentDate']),
        ];

        return new SepaPayment($properties);
    }
}
