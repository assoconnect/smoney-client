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
        ];

        $parser = new PayoutParser();
        $payout = $parser->parse($data);

        $this->assertInstanceOf(Payout::class, $payout);
        $this->assertSame($id, $payout->id);
        $this->assertSame($orderId, $payout->orderId);
        $this->assertSame($amount, $payout->amount);
    }
}
