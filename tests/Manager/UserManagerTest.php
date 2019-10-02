<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Tests\Manager;

use AssoConnect\SMoney\Exception\MissingAppUserIdException;
use AssoConnect\SMoney\Exception\MissingIdException;
use AssoConnect\SMoney\Exception\UserAgeException;
use AssoConnect\SMoney\Exception\UserAlreadyExistsException;
use AssoConnect\SMoney\Exception\UserCountryException;
use AssoConnect\SMoney\Manager\UserManager;
use AssoConnect\SMoney\Object\Address;
use AssoConnect\SMoney\Object\SubAccount;
use AssoConnect\SMoney\Object\User;
use AssoConnect\SMoney\Object\UserProfile;
use AssoConnect\SMoney\Test\SMoneyTestCase;

class UserManagerTest extends SMoneyTestCase
{
    protected function createManager(): UserManager
    {
        $client = $this->getClient();

        return new UserManager($client);
    }

    public function testCreateGetUpdate(): void
    {
        $userManager = $this->createManager();

        $birthdate = new \DateTime();
        $birthdate->setDate(1980, 1, 1);
        $birthdate->setTime(0, 0, 0, 0);

        $userPro = $this->helperCreateUser(true);
        $this->assertNotNull($userPro->id);
        $this->assertSameJson($userPro, $userManager->getUser($userPro->appUserId));

        $userPro->profile->civility = UserProfile::CIVILITY_MRS_MISS;
        $userPro->profile->firstname = 'newName';
        $userPro->profile->lastname = 'newLastName';
        $userPro->profile->birthdate = new \DateTime('2000-01-01T00:00:00');
        $userPro->profile->email = 'new-' . uniqid() . '@new.com';
        $userPro->profile->address->street = 'newStreet';
        $userPro->profile->address->zipcode = 'newZipCode';
        $userPro->profile->address->city = 'newCity';

        $userManager->updateUser($userPro);

        $this->assertSameJson($userPro, $userManager->getUser($userPro->appUserId));

        $user = $this->helperCreateUser($pro = false);
        $this->assertNotNull($user->id);
        $this->assertSameJson($user, $userManager->getUser($user->appUserId));

        $user->profile->civility = UserProfile::CIVILITY_MRS_MISS;
        $user->profile->firstname = 'newName';
        $user->profile->lastname = 'newLastName';
        $user->profile->birthdate = new \DateTime('2000-01-01T00:00:00');
        $user->profile->email = 'new-' . uniqid() . '@new.com';
        $user->profile->address->street = 'newStreet';
        $user->profile->address->zipcode = 'newZipCode';
        $user->profile->address->city = 'newCity';

        $userManager->updateUser($user);

        $this->assertSameJson($user, $userManager->getUser($user->appUserId));

        //Testing SubAccount

        $subAccount = new SubAccount([
            'appAccountId' => uniqid(),
            'displayName' => 'SubAccountName',
        ]);

        $subAccount = $userManager->createSubAccount($user, $subAccount);
        $this->assertNotNull($subAccount->id);

        $_subAccount = $userManager->getSubAccount($user->appUserId, $subAccount->appAccountId);
        $this->assertSameJson($subAccount, $_subAccount);

        $subAccount->displayName = 'NewName';

        $userManager->updateSubAccount($user, $subAccount);

        $_subAccount = $userManager->getSubAccount($user->appUserId, $subAccount->appAccountId);
        $this->assertSameJson($subAccount, $_subAccount);
    }

    public function providerCreateUpdateUserException(): iterable
    {
        $sets = [];

        // User must be 18 years old
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
        $sets[] = [$user, UserAgeException::class];

        // User must live in the right country
        $birthdate = new \DateTime();
        $birthdate->setDate(2000, 1, 1);
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
                    'country' => 'XX',
                ]),
                'email' => 'test-' . uniqid() . '@test.com',
            ]),
        ]);
        $sets[] = [$user, UserCountryException::class];

        // User must have an appUserId
        $birthdate = new \DateTime();
        $birthdate->setDate(2000, 1, 1);
        $birthdate->setTime(0, 0, 0, 0);

        $user = new User([
            'appUserId' => '',
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
        $sets[] = [$user, MissingAppUserIdException::class];

        return $sets;
    }

    /**
     * @dataProvider providerCreateUserException()
     */
    public function testCreateUserException(User $user, string $exception): void
    {
        $userManager = $this->createManager();

        $this->expectException($exception);
        $userManager->createUser($user);
    }

    public function providerCreateUserException(): iterable
    {
        $sets = $this->providerCreateUpdateUserException();

        // User must not have an S-Money Id
        $birthdate = new \DateTime();
        $birthdate->setDate(2000, 1, 1);
        $birthdate->setTime(0, 0, 0, 0);

        $user = new User([
            'id' => 123,
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
        $sets[] = [$user, UserAlreadyExistsException::class];

        return $sets;
    }

    /**
     * @dataProvider providerUpdateUserException()
     */
    public function testUpdateUserException(User $user, string $exception): void
    {
        $userManager = $this->createManager();

        $this->expectException($exception);
        $userManager->updateUser($user);
    }

    public function providerUpdateUserException(): iterable
    {
        $sets = $this->providerCreateUpdateUserException();

        // User must have an S-Money Id
        $birthdate = new \DateTime();
        $birthdate->setDate(2000, 1, 1);
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
        $sets[] = [$user, MissingIdException::class];

        return $sets;
    }
}
