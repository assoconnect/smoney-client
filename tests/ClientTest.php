<?php

namespace AssoConnect\Tests;

use AssoConnect\SMoney\Client;
use AssoConnect\SMoney\Object\Address;
use AssoConnect\SMoney\Object\Company;
use AssoConnect\SMoney\Object\SubAccount;
use AssoConnect\SMoney\Object\User;
use AssoConnect\SMoney\Object\UserProfile;
use GuzzleHttp\Exception\ClientException;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    protected function createClient(): Client
    {
        $token = getenv('SMONEY_TOKEN');
        $endpoint = getenv('SMONEY_ENDPOINT');

        $guzzleClient = new \GuzzleHttp\Client();

        $client = new Client($endpoint, $token, $guzzleClient);

        return $client;
    }

    public function testCreateGetUpdate()
    {
        $client = $this->createClient();
        $birthdate = new \DateTime();
        $birthdate->setDate(1980, 1, 1);
        $birthdate->setTime(0, 0, 0, 0);

        //Testing professional user
        $userPro = new User([
            'appUserId' => 'appuserid-' . uniqid(),
            'type' => User::TYPE_PROFESSIONAL_CLIENT,
            'profile' => new UserProfile([
                'civility' => UserProfile::CIVILITY_MR,
                'firstname' => 'Test',
                'lastname' => 'McTestington',
                'birthdate' => $birthdate,
                'address' => new Address([
                    'street' => 'rue du Test',
                    'zipcode' => '75002',
                    'city' => 'TestVille',
                    'country' => 'FR',
                ]),
                'email' => 'test-' . uniqid() . '@test.com',
            ]),
            'company' => new Company([
                'name' => 'CompanyName',
                'siret' => '123456789',
                'nafCode' => '4741Z',
            ])
        ]);

        $userPro = $client->createUser($userPro);
        $this->assertNotNull($userPro->id);
        $this->assertSame(json_encode($userPro), json_encode($client->getUser($userPro->appUserId)));

        $userPro->profile->civility = UserProfile::CIVILITY_MRS_MISS;
        $userPro->profile->firstname = 'newName';
        $userPro->profile->lastname = 'newLastName';
        $userPro->profile->birthdate = new \DateTime('2000-01-01T00:00:00');
        $userPro->profile->email = 'new-' . uniqid() . '@new.com';
        $userPro->profile->address->street = 'newStreet';
        $userPro->profile->address->zipcode = 'newZipCode';
        $userPro->profile->address->city = 'newCity';

        $client->updateUser($userPro);

        $this->assertSame(json_encode($userPro), json_encode($client->getUser($userPro->appUserId)));

        // Testing individual user
        $user = new User([
            'appUserId' => 'appuserid-' . uniqid(),
            'type' => User::TYPE_INDIVIDUAL_CLIENT,
            'profile' => new UserProfile([
                'civility' => UserProfile::CIVILITY_MR,
                'firstname' => 'Test',
                'lastname' => 'McTestington',
                'birthdate' => $birthdate,
                'address' => new Address([
                    'street' => 'rue du Test',
                    'zipcode' => '75002',
                    'city' => 'TestVille',
                    'country' => 'FR',
                ]),
                'email' => 'test-' . uniqid() . '@test.com',
            ]),
        ]);

        $user = $client->createUser($user);
        $this->assertNotNull($user->id);
        $this->assertSame(json_encode($user), json_encode($client->getUser($user->appUserId)));

        $user->profile->civility = UserProfile::CIVILITY_MRS_MISS;
        $user->profile->firstname = 'newName';
        $user->profile->lastname = 'newLastName';
        $user->profile->birthdate = new \DateTime('2000-01-01T00:00:00');
        $user->profile->email = 'new-' . uniqid() . '@new.com';
        $user->profile->address->street = 'newStreet';
        $user->profile->address->zipcode = 'newZipCode';
        $user->profile->address->city = 'newCity';

        $client->updateUser($user);

        $this->assertSame(json_encode($user), json_encode($client->getUser($user->appUserId)));

        //Testing SubAccount

        $subAccount = new SubAccount([
            'appAccountId' => uniqid(),
            'displayName' => 'SubAccountName',
        ]);

        $subAccount = $client->createSubAccount($user->appUserId, $subAccount);
        $this->assertNotNull($subAccount->id);

        $_subAccount = $client->getSubAccount($user->appUserId, $subAccount->appAccountId);
        $this->assertSame(json_encode($subAccount), json_encode($_subAccount));

        $subAccount->displayName = 'NewName';

        $client->updateSubAccount($user->appUserId, $subAccount);

        $_subAccount = $client->getSubAccount($user->appUserId, $subAccount->appAccountId);
        $this->assertSame(json_encode($subAccount), json_encode($_subAccount));
    }

    public function testException()
    {
        $client = $this->createClient();
        //Creating a user under 18 years old
        $birthdate = new \DateTime();
        $birthdate->setDate(2014, 1, 1);
        $birthdate->setTime(0, 0, 0, 0);

        $user = new User([
            'appUserId' => 'appuserid-' . uniqid(),
            'profile' => new UserProfile([
                'civility' => UserProfile::CIVILITY_MR,
                'firstname' => 'Test',
                'lastname' => 'McTestington',
                'birthdate' => $birthdate,
                'address' => new Address([
                    'street' => 'rue du Test',
                    'zipcode' => '75002',
                    'city' => 'TestVille',
                    'country' => 'FR',
                ]),
                'email' => 'test-' . uniqid() . '@test.com',
            ]),
        ]);

        $this->expectException(\RuntimeException::class);
        $client->createUser($user);
    }
}
