<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Tests\Manager;

use AssoConnect\SMoney\Manager\CardPaymentManager;
use AssoConnect\SMoney\Manager\UserManager;
use AssoConnect\SMoney\Object\CardPayment;
use AssoConnect\SMoney\Object\CardSubPayment;
use AssoConnect\SMoney\Object\SubAccount;
use AssoConnect\SMoney\Test\SMoneyTestCase;

class CardPaymentManagerTest extends SMoneyTestCase
{
    protected function createManager(): CardPaymentManager
    {
        $client = $this->getClient();

        return new CardPaymentManager($client);
    }

    public function testCreateRetrieveCardPayment()
    {
        $client = $this->getClient();
        $manager = $this->createManager();

        $user = $this->helperCreateUser(true);

        $subAccount = new SubAccount([
            'appAccountId' => uniqid(),
        ]);

        $userManager = new UserManager($client);
        $subAccount = $userManager->createSubAccount($user->appUserId, $subAccount);
        $this->assertNotNull($subAccount->id);

        $params = [
            'orderId' => 'test1' . uniqid(),
            'beneficiary' => ['appaccountid' => $subAccount->appAccountId],
            'amount' => 200,
        ];
        $cardSubPayment1 = new CardSubPayment($params);

        $params = [
            'orderId' => 'test2'  . uniqid(),
            'beneficiary' => ['appaccountid' => $subAccount->appAccountId],
            'amount' => 300,
        ];
        $cardSubPayment2 = new CardSubPayment($params);

        $params = [
            'orderId' => 'testA' . uniqid(),
            'isMine' => false,
            'cardSubPayments' => [$cardSubPayment1, $cardSubPayment2],
            'require3DS' => true,
            'urlReturn' => 'http://test.com/returnurl/',
            'amount' => 500,
        ];

        $cardPayment = new CardPayment($params);

        $manager->createCardPayment($cardPayment);
        $this->assertNotNull($cardPayment->id);

        $retrievedCardPayment = $manager->retrieveCardPayment($cardPayment->orderId);

        $this->assertSame($cardPayment->id, $retrievedCardPayment->id);
        $this->assertSame($retrievedCardPayment->subPayments[0]->orderId, $cardSubPayment1->orderId);
        $this->assertSame($retrievedCardPayment->subPayments[1]->orderId, $cardSubPayment2->orderId);

        $retrievedSubPayment1 = $manager->retrieveCardSubPayment($cardPayment->orderId, $cardSubPayment1->orderId);
        $this->assertNotNull($retrievedSubPayment1->id);

        $retrievedSubPayment2 = $manager->retrieveCardSubPayment($cardPayment->orderId, $cardSubPayment2->orderId);
        $this->assertSame($retrievedSubPayment2->amount, $cardSubPayment2->amount);
    }
}
