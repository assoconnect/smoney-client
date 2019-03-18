<?php

namespace AssoConnect\Tests;

use AssoConnect\SMoney\Client;
use AssoConnect\SMoney\Exception\InvalidSignatureException;
use AssoConnect\SMoney\Object\Address;
use AssoConnect\SMoney\Object\BankAccount;
use AssoConnect\SMoney\Object\Company;
use AssoConnect\SMoney\Object\KYC;
use AssoConnect\SMoney\Object\SubAccount;
use AssoConnect\SMoney\Object\User;
use AssoConnect\SMoney\Object\UserProfile;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\UploadedFile;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    protected function createClient(): Client
    {
        $token = getenv('SMONEY_TOKEN');
        $endpoint = getenv('SMONEY_ENDPOINT');
        $signature = getenv('SMONEY_SIGNATURE');

        $guzzleClient = new \GuzzleHttp\Client();

        $client = new Client($endpoint, $token, $guzzleClient, $signature);

        return $client;
    }

    protected function helperCreateUser(bool $pro) :User
    {
        $client = $this->createClient();
        $birthdate = new \DateTime();
        $birthdate->setDate(1980, 1, 1);
        $birthdate->setTime(0, 0, 0, 0);

        if ($pro === true) {
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

            return $client->createUser($userPro);
        } else {
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
            return $client->createUser($user);
        }
    }

    public function testCreateGetUpdate()
    {
        $client = $this->createClient();
        $birthdate = new \DateTime();
        $birthdate->setDate(1980, 1, 1);
        $birthdate->setTime(0, 0, 0, 0);

        $userPro = $this->helperCreateUser($pro = true);
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

        $user = $this->helperCreateUser($pro = false);
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

        $subAccount = $client->createSubAccount($user, $subAccount);
        $this->assertNotNull($subAccount->id);

        $_subAccount = $client->getSubAccount($user, $subAccount->appAccountId);
        $this->assertSame(json_encode($subAccount), json_encode($_subAccount));

        $subAccount->displayName = 'NewName';

        $client->updateSubAccount($user, $subAccount);

        $_subAccount = $client->getSubAccount($user, $subAccount->appAccountId);
        $this->assertSame(json_encode($subAccount), json_encode($_subAccount));
    }

    public function testCreateUserException()
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

        $this->expectException(ClientException::class);
        $client->createUser($user);
    }

    public function testCreateGetUpdateDeleteBankAccount()
    {
        $user = $this->helperCreateUser($pro = true);

        $params = [
            'displayName' => 'bank account',
            'bic' => 'CMCIFR2A',
            'iban' => 'FR7610011000201234567890188',
        ];
        $bankAccount = new BankAccount($params);

        $client = $this->createClient();
        $client->createBankAccount($user, $bankAccount);

        $this->assertNotNull($bankAccount->id);
        $this->assertNotNull($bankAccount->status);

        $this->assertSame(json_encode($bankAccount), json_encode($client->getBankAccount($user, $bankAccount)));

        $bankAccount->displayName = 'newName';

        $client->updateBankAccount($user, $bankAccount);

        $this->assertSame(json_encode($bankAccount), json_encode($client->getBankAccount($user, $bankAccount)));

        $this->assertTrue($client->DeleteBankAccount($user, $bankAccount));
        $this->assertNull($bankAccount->id);
    }

    public function testVerifySignatureValid()
    {
        $client = $this->createClient();

        $body = 'orderId=123456&amount=1020&CallbackSignature=814de6e4d24008b1764fe093026b5127cddbf6c2';
        $request = new Request('POST', 'uri', [], $body, 1.1);

        $client->verifySignature($request);

        $this->expectNotToPerformAssertions();
    }

    public function testVerifySignatureInvalid()
    {
        $client = $this->createClient();

        $body = 'orderId=123456&amount=1020&CallbackSignature=invalid_signature';
        $request = new Request('POST', 'uri', [], $body, 1.1);

        $this->expectException(InvalidSignatureException::class);
        $this->expectExceptionMessage('Invalid signature');
        $client->verifySignature($request);
    }

    public function testVerifySignatureMissing()
    {
        $client = $this->createClient();

        $body = 'orderId=123456&amount=1020';
        $request = new Request('POST', 'uri', [], $body, 1.1);

        $this->expectException(InvalidSignatureException::class);
        $this->expectExceptionMessage('Missing signature');
        $client->verifySignature($request);
    }

    public function testSubmitKYCAccountRequest()
    {
        $client = $this->createClient();

        $userPro = $this->helperCreateUser($pro = true);

        $params = [
            'displayName' => 'bank account',
            'bic' => 'CMCIFR2A',
            'iban' => 'FR7610011000201234567890188',
        ];
        $bankAccount = new BankAccount($params);

        $client->createBankAccount($userPro, $bankAccount);

        $file = __DIR__ . '/data/sample.pdf';
        $stream = fopen($file, 'r+');
        $filesize = filesize($file);
        $bankDetails = new UploadedFile($stream, $filesize, UPLOAD_ERR_OK, 'sample.pdf', 'application/pdf');

        $this->markTestSkipped('S-Money validates new account and thus prevents to submit a KYC request');
        $client->submitKYCAccountRequest($userPro, $bankAccount, $bankDetails);
    }

    public function testCreateKYCRequestRetrieveKYCRequest()
    {
        $client = $this->createClient();

        $userPro = $this->helperCreateUser($pro = true);

        $file1 = __DIR__ . '/data/image2.jpg';
        $stream1 = fopen($file1, 'r+');
        $filesize1 = filesize($file1);

        $file2 = __DIR__ . '/data/image2.png';
        $stream2 = fopen($file2, 'r+');
        $filesize2 = filesize($file2);

        $file1 = new UploadedFile($stream1, $filesize1, UPLOAD_ERR_OK, 'image2.jpg', 'image/jpeg');
        $file2 = new UploadedFile($stream2, $filesize2, UPLOAD_ERR_OK, 'image2.png', 'image/png');


        $files = [
            'address' => $file1,
            'id' => $file2,
        ];

        $kyc = $client->createKYCrequest($userPro, $files);

        $this->assertSame(KYC::STATUS_PENDING, $kyc->status);

        $this->assertSame($kyc->id, $client->retrieveKYCRequest($userPro)->id);
    }
}
