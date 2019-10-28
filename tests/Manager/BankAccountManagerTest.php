<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Tests\Manager;

use AssoConnect\SMoney\Exception\MissingBicException;
use AssoConnect\SMoney\Exception\MissingIbanException;
use AssoConnect\SMoney\Manager\BankAccountManager;
use AssoConnect\SMoney\Object\BankAccount;
use AssoConnect\SMoney\Test\SMoneyTestCase;

class BankAccountManagerTest extends SMoneyTestCase
{
    protected function createManager(): BankAccountManager
    {
        $client = $this->getClient();

        return new BankAccountManager($client);
    }

    public function testCreateGetUpdateDeleteBankAccount()
    {
        $user = $this->helperCreateUser(true);

        $params = [
            'displayName' => 'bank account',
            'bic' => 'SOGEFRPPXXX',
            'iban' => 'FR7630003031100005055686128',
        ];
        $bankAccount = new BankAccount($params);

        $client = $this->getClient();
        $bankAccountManager = new BankAccountManager($client);
        $bankAccountManager->createBankAccount($user->appUserId, $bankAccount);

        $this->assertNotNull($bankAccount->id);
        $this->assertNotNull($bankAccount->status);

        $this->assertSameJson($bankAccount, $bankAccountManager->getBankAccount($user->appUserId, $bankAccount->id));

        $bankAccount->displayName = 'newName';

        $bankAccountManager->updateBankAccount($user->appUserId, $bankAccount);

        $this->assertSameJson($bankAccount, $bankAccountManager->getBankAccount($user->appUserId, $bankAccount->id));

        $this->assertTrue($bankAccountManager->deleteBankAccount($user->appUserId, $bankAccount));
        $this->assertNull($bankAccount->id);
    }

    /**
     * @dataProvider providerCreateBankAccountException()
     */
    public function testCreateBankAccountException(BankAccount $bankAccount, string $exception): void
    {
        $bankAccountManager = $this->createManager();

        $user = $this->helperCreateUser(true);

        $this->expectException($exception);
        $bankAccountManager->createBankAccount($user->appUserId, $bankAccount);
    }

    public function providerCreateBankAccountException(): iterable
    {
        // Empty Iban
        $params = [
            'displayName' => 'bank account',
            'bic' => 'CMCIFR2A',
            'iban' => '',
        ];
        $bankAccount = new BankAccount($params);

        $sets[] = [$bankAccount, MissingIbanException::class];

        // Iban is null
        $params = [
            'displayName' => 'bank account',
            'bic' => 'CMCIFR2A',
            'iban' => null,
        ];
        $bankAccount = new BankAccount($params);

        $sets[] = [$bankAccount, MissingIbanException::class];

        // Empty Bic
        $params = [
            'displayName' => 'bank account',
            'bic' => '',
            'iban' => 'FR7610011000201234567890188',
        ];
        $bankAccount = new BankAccount($params);

        $sets[] = [$bankAccount, MissingBicException::class];

        // Bic is null
        $params = [
            'displayName' => 'bank account',
            'bic' => null,
            'iban' => 'FR7610011000201234567890188',
        ];
        $bankAccount = new BankAccount($params);

        $sets[] = [$bankAccount, MissingBicException::class];

        return $sets;
    }
}
