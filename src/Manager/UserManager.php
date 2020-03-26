<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Manager;

use AssoConnect\SMoney\Client;
use AssoConnect\SMoney\Exception\MissingAppUserIdException;
use AssoConnect\SMoney\Exception\MissingIdException;
use AssoConnect\SMoney\Exception\UserAgeException;
use AssoConnect\SMoney\Exception\UserAlreadyExistsException;
use AssoConnect\SMoney\Exception\UserCountryException;
use AssoConnect\SMoney\Object\Address;
use AssoConnect\SMoney\Object\Company;
use AssoConnect\SMoney\Object\SubAccount;
use AssoConnect\SMoney\Object\User;
use AssoConnect\SMoney\Object\UserProfile;
use Fig\Http\Message\RequestMethodInterface;
use Psr\Http\Message\ResponseInterface;

class UserManager
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
     * Creating a S-Money User
     * @param User $user
     * @return User
     */
    public function createUser(User $user): User
    {
        $this->checkUser($user);

        if (empty($user->id) === false) {
            throw new UserAlreadyExistsException('User must not have an S-Money id to be created');
        }

        $path = '/users';
        $method = RequestMethodInterface::METHOD_POST;
        $data = $this->formatData($user);
        $data['appuserid'] = $user->appUserId;
        $data['type'] = $user->type;

        $response = $this->client->query($path, $method, $data);

        $this->parseResponse($response, $user);

        return $user;
    }

    /**
     * Updating the given S-Money User
     * @param User $user
     * @return User
     */
    public function updateUser(User $user): User
    {
        $this->checkUser($user);

        if (empty($user->id)) {
            throw new MissingIdException('User must have an S-Money id to be updated');
        }

        $path = '/users/' . $user->appUserId;
        $method = RequestMethodInterface::METHOD_PUT;
        $data = $this->formatData($user);

        $response = $this->client->query($path, $method, $data);

        $this->parseResponse($response, $user);

        return $user;
    }

    private function formatData(User $user): array
    {
        $data = [
            'profile' => [
                'civility' => $user->profile->civility,
                'firstname' => $user->profile->firstname,
                'lastname' => $user->profile->lastname,
                'birthdate' => $user->profile->birthdate->format('c'),
                'address' => [
                    'street' => $user->profile->address->street,
                    'zipcode' => $user->profile->address->zipcode,
                    'city' => $user->profile->address->city,
                    'country' => $user->profile->address->country,
                ],
                'email' => $user->profile->email,
            ]
        ];

        if (User::TYPE_PROFESSIONAL_CLIENT === $user->type && $user->company) {
            $data['company'] = [
                'name' => $user->company->name,
                'SIRET'   => $user->company->siret,
                'NAFCode' => $user->company->nafCode,
            ];
        }

        if (User::TYPE_INDIVIDUAL_CLIENT === $user->type) {
            $data['profile']['CSPCode'] = $user->profile->csp ?? 54;
        }

        return $data;
    }

    private function parseResponse(ResponseInterface $response, User $user): void
    {
        $data = json_decode($response->getBody()->__toString(), true);

        $user->id = $data['Id'];
        $user->status = $data['Status'];
        $user->role = $data['Role'];
    }

    /**
     * Checking that the user object is valid according to S-Money validations
     * @param User $user
     */
    private function checkUser(User $user): void
    {
        if (in_array($user->profile->address->country, Address::COUNTRIES) === false) {
            throw new UserCountryException('The User\'s country is not accepted by S-Money');
        }

        $limitAge = new \DateTime('-18 years');
        if ($user->profile->birthdate > $limitAge) {
            throw new UserAgeException('The User must be over 18 years old');
        }

        if (empty($user->appUserId)) {
            throw new MissingAppUserIdException('User must have an appUserId');
        }
    }

    /**
     * Retrieving the S-Money User info based on its appUserId
     * @param string $appUserId
     * @return User
     */
    public function getUser(string $appUserId): User
    {
        $path = '/users/' . $appUserId;
        $method = RequestMethodInterface::METHOD_GET;
        $res = $this->client->query($path, $method);

        $data = json_decode($res->getBody()->__toString(), true);

        if ($data['Type'] === User::TYPE_PROFESSIONAL_CLIENT) {
            $companyData = [
                'name' => $data['Company']['Name'],
                'siret'   => $data['Company']['SIRET'],
                'nafCode' => $data['Company']['NAFCode'],
            ];
            $company = new Company($companyData);
        } else {
            $company = null;
        }

        $addressData = [
            'street' => $data['Profile']['Address']['Street'],
            'zipcode' => $data['Profile']['Address']['ZipCode'],
            'city' => $data['Profile']['Address']['City'],
            'country' => $data['Profile']['Address']['Country'],
        ];
        $userProfileData = [
            'civility' => $data['Profile']['Civility'],
            'firstname' => $data['Profile']['FirstName'],
            'lastname' => $data['Profile']['LastName'],
            'birthdate' => new \DateTime($data['Profile']['Birthdate']),
            'address' => new Address($addressData),
            'email' => $data['Profile']['Email'],
            'csp' => $data['Profile']['CSPCode'],
        ];

        $userData = [
            'id' => $data['Id'],
            'status' => $data['Status'],
            'appUserId' => $data['AppUserId'],
            'type' => $data['Type'],
            'profile' => new UserProfile($userProfileData),
            'company' => $company,
            'role' => $data['Role'],
        ];
        return new User($userData);
    }

    /**
     * Creating a S-Money SubAccount linked to the given User
     * @param string $appUserId
     * @param SubAccount $subAccount
     * @return SubAccount
     */
    public function createSubAccount(string $appUserId, SubAccount $subAccount): SubAccount
    {
        $path = '/users/' . $appUserId . '/subaccounts';
        $method = RequestMethodInterface::METHOD_POST;
        $data = [
            'appaccountid' => $subAccount->appAccountId,
            'displayname'  => $subAccount->displayName,
        ];

        $res = $this->client->query($path, $method, $data);
        $data = json_decode($res->getBody()->__toString(), true);
        $subAccount->id = $data['Id'];

        return $subAccount;
    }

    /**
     * Updating the S-Money SubAccount's display name
     * @param string $appUserId
     * @param SubAccount $subAccount
     * @return SubAccount
     */
    public function updateSubAccount(string $appUserId, SubAccount $subAccount): SubAccount
    {
        $path = '/users/' . $appUserId . '/subaccounts/' . $subAccount->appAccountId;
        $method = RequestMethodInterface::METHOD_PUT;
        $data = [
            'displayName' => $subAccount->displayName,
        ];

        $this->client->query($path, $method, $data);

        return $subAccount;
    }

    /**
     * Retrieving the S-Money SubAccount info
     * @param string $appUserId
     * @param string $appAccountId
     * @return SubAccount
     */
    public function getSubAccount(string $appUserId, string $appAccountId): SubAccount
    {
        $path = '/users/' . $appUserId . '/subaccounts/' . $appAccountId;
        $method = RequestMethodInterface::METHOD_GET;
        $res = $this->client->query($path, $method);

        $data = json_decode($res->getBody()->__toString(), true);

        $subAccountData = [
            'id' => $data['Id'],
            'appAccountId' => $data['AppAccountId'],
            'displayName' => $data['DisplayName'],
            'amount' => $data['Amount'],
        ];
        return new SubAccount($subAccountData);
    }
}
