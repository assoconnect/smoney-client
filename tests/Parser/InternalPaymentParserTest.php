<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Tests\Parser;

use AssoConnect\SMoney\Object\InternalPayment;
use AssoConnect\SMoney\Parser\InternalPaymentParser;
use PHPUnit\Framework\TestCase;

class InternalPaymentParserTest extends TestCase
{
    public function testParserWorks(): void
    {
        $data = [
            'Id'            => $id = 123,
            'OrderId'       => $orderId = 'order id',
            'Amount'        => $amount = 456,
            'PaymentDate'   => $paymentDate = '2013-09-10T15:49:58.791+02:00',
            'Sender'        => [
                'Id'            => $senderId = 789,
                'AppAccountId'  => $senderAppAccountId = 78,
                'DisplayName'   => $senderDisplayName = 'foo sender',
            ],
            'Beneficiary'   => [
                'Id'            => $beneficiaryId = 987,
                'AppAccountId'  => $beneficiaryAppAccountId = 98,
                'DisplayName'   => $beneficiaryDisplayName = 'bar beneficiary',
            ],
        ];

        $parser = new InternalPaymentParser();
        $internalPayment = $parser->parse($data);

        $this->assertInstanceOf(InternalPayment::class, $internalPayment);
        $this->assertSame($id, $internalPayment->id);
        $this->assertSame($orderId, $internalPayment->orderId);
        $this->assertSame($amount, $internalPayment->amount);
        $this->assertSame($paymentDate, $internalPayment->paymentDate->format(DATE_RFC3339_EXTENDED));
        $this->assertSame($senderId, $internalPayment->sender->id);
        $this->assertSame($senderAppAccountId, $internalPayment->sender->appAccountId);
        $this->assertSame($senderDisplayName, $internalPayment->sender->displayName);
        $this->assertSame($beneficiaryId, $internalPayment->beneficiary->id);
        $this->assertSame($beneficiaryAppAccountId, $internalPayment->beneficiary->appAccountId);
        $this->assertSame($beneficiaryDisplayName, $internalPayment->beneficiary->displayName);
    }
}
