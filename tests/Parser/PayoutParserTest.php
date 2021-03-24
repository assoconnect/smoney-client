<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Tests\Parser;

use AssoConnect\SMoney\Object\Payout;
use AssoConnect\SMoney\Parser\PayoutParser;
use PHPUnit\Framework\TestCase;

class PayoutParserTest extends TestCase
{
    public function testParserWorks(): void
    {
        $data = [
            'Id'      => $id = 123,
            'OrderId' => $orderId = 'order id',
            'Amount'  => $amount = 456,
            'OperationDate' => $operationDate = '2021-03-23T15:38:00',
            'Status' => 1,
        ];

        $parser = new PayoutParser();
        $payout = $parser->parse($data);

        $this->assertInstanceOf(Payout::class, $payout);
        $this->assertSame($id, $payout->id);
        $this->assertSame($orderId, $payout->orderId);
        $this->assertSame($amount, $payout->amount);
        $this->assertSame($operationDate, $payout->requestDate->format(DATE_ISO8601));
        $this->assertSame(null, $payout->executedDate);
        $this->assertSame(1, $payout->status);
    }

    public function testParserWithExecutedDateWorks(): void
    {
        $data = [
            'Id'      => $id = 123,
            'OrderId' => $orderId = 'order id',
            'Amount'  => $amount = 456,
            'OperationDate' => $operationDate = '2021-03-23T15:38:00',
            'ExecutedDate' => $executedDate = '2021-03-26T15:38:00',
            'Status' => 1,
        ];

        $parser = new PayoutParser();
        $payout = $parser->parse($data);

        $this->assertInstanceOf(Payout::class, $payout);
        $this->assertSame($id, $payout->id);
        $this->assertSame($orderId, $payout->orderId);
        $this->assertSame($amount, $payout->amount);
        $this->assertSame($operationDate, $payout->requestDate->format(DATE_ISO8601));
        $this->assertSame($executedDate, $payout->executedDate->format(DATE_ISO8601));
        $this->assertSame(1, $payout->status);
    }
}
