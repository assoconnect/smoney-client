<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Tests\Manager;

use AssoConnect\SMoney\Manager\SepaPaymentManager;
use AssoConnect\SMoney\Parser\SepaPaymentParser;
use AssoConnect\SMoney\Test\SMoneyTestCase;

class SepaPaymentManagerTest extends SMoneyTestCase
{
    protected function createManager(): SepaPaymentManager
    {
        $client = $this->getClient();
        $parser = new SepaPaymentParser();

        return new SepaPaymentManager($client, $parser);
    }

    public function testRetrieveSepaPaymentsWorks(): void
    {
        $manager = $this->createManager();
        $manager->retrieveSepaPayments();

        $this->expectNotToPerformAssertions();
    }
}
