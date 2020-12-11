<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Tests\Parser;

use AssoConnect\SMoney\Object\SepaPayment;
use AssoConnect\SMoney\Parser\SepaPaymentParser;
use PHPUnit\Framework\TestCase;

class SepaPaymentParserTest extends TestCase
{
    public function testParserWorks(): void
    {
        $data = [
            'Id' => $id = 123,
            'OrderId' => $orderId = 'app ref',
            'Status' => $status = SepaPayment::STATUS_COMPLETED,
            'Amount' => $amount = 100,
            'PaymentDate' => $paymentDate = '2013-09-10T15:49:58.791+02:00',
        ];

        $parser = new SepaPaymentParser();
        $cardPayment = $parser->parse($data);

        $this->assertSame($id, $cardPayment->id);
        $this->assertSame($orderId, $cardPayment->orderId);
        $this->assertSame($status, $cardPayment->status);
        $this->assertSame($amount, $cardPayment->amount);
        $this->assertSame($paymentDate, $cardPayment->paymentDate->format(DATE_RFC3339_EXTENDED));
    }
}
