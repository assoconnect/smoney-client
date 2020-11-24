<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Tests\Manager;

use AssoConnect\SMoney\Manager\BankCardManager;
use AssoConnect\SMoney\Manager\UserManager;
use AssoConnect\SMoney\Object\BankCardRegistration;
use AssoConnect\SMoney\Object\Card;
use AssoConnect\SMoney\Object\SubAccount;
use AssoConnect\SMoney\Test\SMoneyTestCase;

class BankCardManagerTest extends SMoneyTestCase
{
    protected function createManager(): BankCardManager
    {
        $client = $this->getClient();

        return new BankCardManager($client);
    }

    public function testCreateRetrieveBankCardRegistration()
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

        $cardParams = [
            'appCardId' => 'card' . uniqid(),
            'name' => 'carte bancaire'
        ];

        $card = new Card($cardParams);

        $params = [
            'card' => $card,
            'urlReturn' => 'http://test.com/returnurl/',
            'urlCallback' => 'http://test.com/callbackurl/',
            'availableCards' => 'CB',
            'extraParameters' => [
                'systempaylanguage' => 'en'
            ]
        ];

        $bankCardRegistration = new BankCardRegistration($params);

        $manager->createBankCardRegistration($bankCardRegistration);
        $this->assertSame($bankCardRegistration->status, 0);
        $this->assertNotNull($bankCardRegistration->href);

        $retrievedBankCardRegistration = $manager->retrieveBankCardRegistration(
            $bankCardRegistration->card->appCardId
        );

        $this->assertSame($bankCardRegistration->card->appCardId, $retrievedBankCardRegistration->card->appCardId);
    }
}
