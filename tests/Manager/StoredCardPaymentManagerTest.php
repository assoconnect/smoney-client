<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Tests\Manager;

use AssoConnect\SMoney\Manager\CardPaymentManager;
use AssoConnect\SMoney\Manager\StoredCardPaymentManager;
use AssoConnect\SMoney\Parser\CardPaymentParser;
use AssoConnect\SMoney\Test\SMoneyTestCase;

class StoredCardPaymentManagerTest extends SMoneyTestCase
{
    protected function createManager(): StoredCardPaymentManager
    {
        $client = $this->getClient();
        $parser = new CardPaymentParser();

        return new StoredCardPaymentManager($client, $parser);
    }

    public function testRetrieveCardPaymentsWorks(): void
    {
        $manager = $this->createManager();
        $payments = $manager->retrieveStoredCardPayments();

        $this->assertNotEmpty($payments);
    }
}
