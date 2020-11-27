<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Tests\Manager;

use AssoConnect\SMoney\Exception\InvalidStringException;
use AssoConnect\SMoney\Manager\PayoutManager;
use AssoConnect\SMoney\Parser\PayoutParser;
use AssoConnect\SMoney\Test\SMoneyTestCase;

class PayoutManagerTest extends SMoneyTestCase
{
    protected function createManager(): PayoutManager
    {
        $client = $this->getClient();
        $parser = new PayoutParser();

        return new PayoutManager($client, $parser);
    }

    /**
     * @dataProvider providerInvalidString()
     */
    public function testInvalidReference(string $reference)
    {
        $manager = $this->createManager();

        $this->expectException(InvalidStringException::class);
        $this->expectExceptionMessage(sprintf(InvalidStringException::MESSAGE, 'reference'));

        $manager->createPayout(
            'app user id',
            'order id',
            12,
            'app account id',
            123,
            'hello',
            $reference,
            'motif'
        );
    }

    /**
     * @dataProvider providerInvalidString()
     */
    public function testInvalidMotif(string $motif)
    {
        $manager = $this->createManager();

        $this->expectException(InvalidStringException::class);
        $this->expectExceptionMessage(sprintf(InvalidStringException::MESSAGE, 'motif'));

        $manager->createPayout(
            'app user id',
            'order id',
            12,
            'app account id',
            123,
            'hello',
            'reference',
            $motif
        );
    }

    public function providerInvalidString()
    {
        yield 'too long' => [str_repeat('a', 36)];
        yield 'special char' => ['hello!'];
        yield 'accent' => ['ééé'];
    }

    public function testRetrievePayoutsWorks(): void
    {
        $manager = $this->createManager();
        $payouts = $manager->retrievePayouts();

        $this->assertNotEmpty($payouts);
    }
}
