<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Test;

use AssoConnect\SMoney\Client;
use AssoConnect\SMoney\Manager\UserManager;
use AssoConnect\SMoney\Object\Address;
use AssoConnect\SMoney\Object\Company;
use AssoConnect\SMoney\Object\User;
use AssoConnect\SMoney\Object\UserProfile;
use PHPUnit\Framework\TestCase;

class SMoneyTestCase extends TestCase
{
    /**
     * @var Client
     */
    protected $client;

    public function setUp()
    {
        parent::setUp();
        if ($this->client) {
            $this->client = null;
        }
    }

    protected function getClient(): Client
    {
        // We instanciate a client if it does not exist yet
        if (null === $this->client) {
            $token = getenv('SMONEY_TOKEN');
            $endpoint = getenv('SMONEY_ENDPOINT');
            $signature = getenv('SMONEY_SIGNATURE');

            $guzzleClient = new \GuzzleHttp\Client();

            $this->client = new Client($endpoint, $token, $guzzleClient, $signature);
        }

        return $this->client;
    }

    protected function helperCreateUser(bool $pro): User
    {
        $client = $this->getClient();

        $userManager = new UserManager($client);

        $birthdate = new \DateTime();
        $birthdate->setDate(1980, 1, 1);
        $birthdate->setTime(0, 0, 0, 0);

        $userProfile = new UserProfile([
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
            'csp' => '54',
        ]);

        if ($pro === true) {
            $userPro = new User([
                'appUserId' => 'appuserid-' . uniqid(),
                'type' => User::TYPE_PROFESSIONAL_CLIENT,
                'profile' => $userProfile,
                'company' => new Company([
                    'name' => 'CompanyName',
                    'siret' => '123456789',
                    'nafCode' => '4741Z',
                ])
            ]);

            return $userManager->createUser($userPro);
        } else {
            $user = new User([
                'appUserId' => 'appuserid-' . uniqid(),
                'type' => User::TYPE_INDIVIDUAL_CLIENT,
                'profile' => $userProfile,
            ]);
            return $userManager->createUser($user);
        }
    }

    public function assertSameJson($expected, $actual)
    {
        $this->assertSame(
            json_encode($expected),
            json_encode($actual)
        );
    }
}
