<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Parser;

use AssoConnect\SMoney\Object\CardPayment;
use AssoConnect\SMoney\Object\CardRefund;
use AssoConnect\SMoney\Object\CardSubPayment;

class CardPaymentParser
{
    public function parse(array $data): CardPayment
    {
        $properties = [
            'id'           => $data['Id'],
            'orderId'      => $data['OrderId'],
            'status'       => $data['Status'],
            'type'         => $data['Type'] ?? null,
            'amount'       => $data['Amount'],
            'extraResults' => $data['ExtraResults'] ?? $data['ExtraParameters'] ?? null,
            'errorCode'    => $data['ErrorCode'] ?? null,
            'subPayments'  => [],
            'refunds' => [],
        ];
        if (array_key_exists('PaymentDate', $data)) {
            $properties['paymentDate'] = new \DateTime($data['PaymentDate']);
        } elseif (array_key_exists('OperationDate', $data)) {
            $properties['paymentDate'] = new \DateTime($data['OperationDate']);
        }
        if (array_key_exists('Card', $data)) {
            $properties['card'] = $data['Card'];
        }
        if (array_key_exists('Payments', $data)) {
            foreach ($data['Payments'] as $subPaymentData) {
                $subPaymentProperties = [
                    'id'            => $subPaymentData['Id'],
                    'orderId'       => $subPaymentData['OrderId'],
                    'beneficiary'   => $subPaymentData['Beneficiary'],
                    'amount'        => $subPaymentData['Amount'],
                    'status'        => $subPaymentData['Status'],
                ];
                $properties['subPayments'][] = new CardSubPayment($subPaymentProperties);
            }
        }
        if (array_key_exists('Refunds', $data)) {
            foreach ($data['Refunds'] as $refundData) {
                $refundProperties = [
                    'id'            => $refundData['Id'],
                    'orderId'       => $refundData['OrderId'],
                    'status'        => $refundData['Status'],
                ];
                $properties['refunds'][] = new CardRefund($refundProperties);
            }
        }

        return new CardPayment($properties);
    }
}
