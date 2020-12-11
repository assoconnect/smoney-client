<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Tests\Manager;

use AssoConnect\SMoney\Manager\InternalPaymentManager;
use AssoConnect\SMoney\Parser\InternalPaymentParser;
use AssoConnect\SMoney\Test\SMoneyTestCase;

class InternalPaymentManagerTest extends SMoneyTestCase
{
    protected function createManager(): InternalPaymentManager
    {
        $client = $this->getClient();
        $parser = new InternalPaymentParser();

        return new InternalPaymentManager($client, $parser);
    }

    public function testRetrieveInternalPaymentsWorks(): void
    {
        $manager = $this->createManager();
        $payments = $manager->retrieveInternalPayments();

        $this->assertNotEmpty($payments);
    }
}
