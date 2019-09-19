<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Manager;

use AssoConnect\SMoney\Client;
use AssoConnect\SMoney\Exception\MissingBicException;
use AssoConnect\SMoney\Exception\MissingIbanException;
use AssoConnect\SMoney\Object\BankAccount;
use AssoConnect\SMoney\Object\User;
use Fig\Http\Message\RequestMethodInterface;

class BankAccountManager
{
    /**
     * @var Client
     */
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Creating a S-Money BankAccount for the given User
     * @param User $user
     * @param BankAccount $bankAccount
     * @return BankAccount
     */
    public function createBankAccount(User $user, BankAccount $bankAccount) :BankAccount
    {
        if ($bankAccount->iban === '' or $bankAccount->iban === null) {
            throw new MissingIbanException('The BankAccount must have an IBAN');
        }

        if ($bankAccount->bic === '' or $bankAccount->bic === null) {
            throw new MissingBicException('The BankAccount must have a BIC');
        }

        $path = '/users/' . $user->appUserId . '/bankaccounts';
        $method = RequestMethodInterface::METHOD_POST;
        $data = [
            'displayname' => $bankAccount->displayName,
            'bic'  => $bankAccount->bic,
            'iban'  => $bankAccount->iban,
        ];
        $res = $this->client->query($path, $method, $data);
        $data = json_decode($res->getBody()->__toString(), true);
        $bankAccount->id = $data['Id'];
        $bankAccount->status = $data['Status'];
        return $bankAccount;
    }

    /**
     * Retrieving the BankAccount info
     * @param string $appUserId
     * @param int $bankAccountId
     * @return BankAccount
     */
    public function getBankAccount(string $appUserId, int $bankAccountId) :BankAccount
    {
        $path = '/users/' . $appUserId . '/bankaccounts/' . $bankAccountId;
        $method = RequestMethodInterface::METHOD_GET;
        $res = $this->client->query($path, $method);

        $data = json_decode($res->getBody()->__toString(), true);
        $bankAccountData = [
            'id' => $data['Id'],
            'displayName' => $data['DisplayName'],
            'bic' => $data['Bic'],
            'iban' => $data['Iban'],
            'status' => $data['Status'],
        ];
        return new BankAccount($bankAccountData);
    }

    /**
     * Updating the BankAccount display name based on it's id
     * @param User $user
     * @param BankAccount $bankAccount
     * @return BankAccount
     */
    public function updateBankAccount(User $user, BankAccount $bankAccount) :BankAccount
    {
        $path = '/users/' . $user->appUserId . '/bankaccounts/';
        $method = RequestMethodInterface::METHOD_PUT;
        $data = [
            'id' => $bankAccount->id,
            'displayName' => $bankAccount->displayName,
        ];

        $this->client->query($path, $method, $data);
        return $bankAccount;
    }

    /**
     * Deleting the BankAccount in S-Money
     * @param User $user
     * @param BankAccount $bankAccount
     * @return bool
     */
    public function deleteBankAccount(User $user, BankAccount $bankAccount) :bool
    {
        $path = '/users/' . $user->appUserId . '/bankaccounts/' . $bankAccount->id;
        $method = RequestMethodInterface::METHOD_DELETE;

        $this->client->query($path, $method);
        $bankAccount->id = null;
        return true;
    }
}
