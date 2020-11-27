<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Tests\Parser;

use AssoConnect\SMoney\Object\CardPayment;
use AssoConnect\SMoney\Parser\CardPaymentParser;
use PHPUnit\Framework\TestCase;

class CardPaymentParserTest extends TestCase
{
    public function testParserWorksWithMinimalSetOfData(): void
    {
        $data = [
            'Id' => $id = 123,
            'OrderId' => $orderId = 'app ref',
            'Status' => $status = CardPayment::STATUS_SUCCESS,
            'Amount' => $amount = 100,
        ];

        $parser = new CardPaymentParser();
        $cardPayment = $parser->parse($data);

        $this->assertSame($id, $cardPayment->id);
        $this->assertSame($orderId, $cardPayment->orderId);
        $this->assertSame($status, $cardPayment->status);
        $this->assertSame($amount, $cardPayment->amount);
    }

    public function testParserWorksWithFullSetOfData(): void
    {
        $data = [
            'Id' => 123,
            'OrderId' => 'app ref',
            'Status' => CardPayment::STATUS_SUCCESS,
            'Type' => $type = CardPayment::TYPE_PAYMENT,
            'Amount' => 100,
            'PaymentDate' => $paymentDate = '2013-09-10T15:49:58.791+02:00',
            'ExtraResults' => $extraResult = ['foo' => 'bar'],
            'ErrorCode' => $errorCode = 456,
            'Card' => $card = ['foo' => 'card'],
            'Payments' => [
                [
                    'Id' => $id = 123,
                    'OrderId' => $orderId = 'app ref',
                    'Beneficiary' => $beneficiary = 'app benef',
                    'Status' => $status = CardPayment::STATUS_SUCCESS,
                    'Amount' => $amount = 100,
                ]
            ],
        ];

        $parser = new CardPaymentParser();
        $cardPayment = $parser->parse($data);

        // CardPayment
        $this->assertSame($type, $cardPayment->type);
        $this->assertSame($paymentDate, $cardPayment->paymentDate->format(DATE_RFC3339_EXTENDED));
        $this->assertSame($extraResult, $cardPayment->extraResults);
        $this->assertSame($errorCode, $cardPayment->errorCode);

        // Card
        $this->assertSame($card, $cardPayment->card);

        // CardSubPayments
        $cardSubPayment = $cardPayment->subPayments[0];
        $this->assertSame($id, $cardSubPayment->id);
        $this->assertSame($orderId, $cardSubPayment->orderId);
        $this->assertSame($beneficiary, $cardSubPayment->beneficiary);
        $this->assertSame($status, $cardSubPayment->status);
        $this->assertSame($amount, $cardSubPayment->amount);
    }

    public function testParserWorksWithOperationDate(): void
    {
        $data = [
            'Id' => $id = 123,
            'OrderId' => $orderId = 'app ref',
            'Status' => $status = CardPayment::STATUS_SUCCESS,
            'Amount' => $amount = 100,
            'OperationDate' => $paymentDate = '2013-09-10T15:49:58.791+02:00',
        ];

        $parser = new CardPaymentParser();
        $cardPayment = $parser->parse($data);

        $this->assertSame($id, $cardPayment->id);
        $this->assertSame($orderId, $cardPayment->orderId);
        $this->assertSame($status, $cardPayment->status);
        $this->assertSame($amount, $cardPayment->amount);
        $this->assertSame($paymentDate, $cardPayment->paymentDate->format(DATE_RFC3339_EXTENDED));
    }
}
