<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Parser;

use AssoConnect\SMoney\Object\InternalPayment;
use AssoConnect\SMoney\Object\SubAccount;

class InternalPaymentParser
{
    public function parse(array $data): InternalPayment
    {
        $sender = new SubAccount([
            'id'            => $data['Sender']['Id'],
            'appAccountId'  => $data['Sender']['AppAccountId'],
            'displayName'   => $data['Sender']['DisplayName'],
        ]);

        $beneficiary = new SubAccount([
            'id'            => $data['Beneficiary']['Id'],
            'appAccountId'  => $data['Beneficiary']['AppAccountId'],
            'displayName'   => $data['Beneficiary']['DisplayName'],
        ]);

        $properties = [
            'id'           => $data['Id'],
            'orderId'      => $data['OrderId'],
            'amount'       => $data['Amount'],
            'paymentDate'  => new \DateTime($data['PaymentDate']),
            'sender'       => $sender,
            'beneficiary'  => $beneficiary,
        ];

        return new InternalPayment($properties);
    }
}
