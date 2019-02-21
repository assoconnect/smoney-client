<?php

declare(strict_types=1);

namespace AssoConnect\SMoney;

use AssoConnect\SMoney\Exception\InvalidSignatureException;
use AssoConnect\SMoney\Object\Address;
use AssoConnect\SMoney\Object\Company;
use AssoConnect\SMoney\Object\KYC;
use AssoConnect\SMoney\Object\SubAccount;
use AssoConnect\SMoney\Object\User;
use AssoConnect\SMoney\Object\UserProfile;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;

class Client
{
    /**
     * Guzzle Client
     *
     * @var ClientInterface
     */
    protected $client;

    /**
     * S-Money endpoint
     *
     * @var string
     */
    protected $endpoint;

    /**
     * Client's S-Money token
     *
     * @var string
     */
    protected $token;

    /**
     * S-Money signature
     *
     * @var String
     */
    protected $signature;

    public function __construct(string $endpoint, string $token, ClientInterface $client, string $signature)
    {
        $this->client = $client;
        $this->endpoint = $endpoint;
        $this->token = $token;
        $this->signature = $signature;
    }

    /**
     * Queries to the S-Money API
     *
     * @param  string        $path    Endpoint path
     * @param  string        $method  Request method
     * @param  iterable|null $data    Query data
     * @param  int           $version API Version
     * @return Response
     */
    protected function query(string $path, string $method, iterable $data = null, int $version = 1): ResponseInterface
    {
        if ($method === 'GET') {
            $options = [
                'headers' => [
                    'Accept'        => 'application/vnd.s-money.v' . $version . '+json',
                    'Authorization' => 'Bearer ' . $this->token,
                ]
            ];
        }

        if ($method === 'POST' or $method === 'PUT') {
            $options = [
                'json'          => $data,
                'headers'       => [
                    'Accept'        => 'application/vnd.s-money.v' . $version . '+json',
                    'Content-Type'  => 'application/vnd.s-money.v' . $version . '+json',
                    'Authorization' => 'Bearer ' . $this->token,
                ]
            ];
        }

        return $this->client->request($method, $this->endpoint . $path, $options);
    }

    public function createUser(User $user): User
    {
        $path = '/users';
        $method = 'POST';

        $data = [
            'appuserid' => $user->appUserId,
            'type' => $user->type,
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

        if ($user->type === User::TYPE_PROFESSIONAL_CLIENT) {
            $data['company'] = [
                'name' => $user->company->name,
                'SIRET'   => $user->company->siret,
                'NAFCode' => $user->company->nafCode,
            ];
        }

        $res = $this->query($path, $method, $data);
        $data = json_decode($res->getBody()->__toString(), true);
        $user->id = $data['Id'];
        return $user;
    }

    public function updateUser(User $user): User
    {
        $path = '/users/' . $user->appUserId;
        $method = 'PUT';
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

        $this->query($path, $method, $data);
        return $user;
    }

    public function getUser(string $appUserId): User
    {
        $path = '/users/' . $appUserId;
        $method = 'GET';
        $res = $this->query($path, $method);

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
        ];

        $userData = [
            'id' => $data['Id'],
            'appUserId' => $data['AppUserId'],
            'type' => $data['Type'],
            'profile' => new UserProfile($userProfileData),
            'company' => $company,
        ];
        $user = new User($userData);

        return $user;
    }

    public function createSubAccount(string $appUserId, SubAccount $subAccount): SubAccount
    {
        $path = '/users/' . $appUserId . '/subaccounts';
        $method = 'POST';
        $data = [
            'appaccountid' => $subAccount->appAccountId,
            'displayname'  => $subAccount->displayName,
        ];

        $res = $this->query($path, $method, $data);
        $data = json_decode($res->getBody()->__toString(), true);
        $subAccount->id = $data['Id'];
        return $subAccount;
    }

    public function updateSubAccount(string $appUserId, SubAccount $subAccount): SubAccount
    {
        $path = '/users/' . $appUserId . '/subaccounts/' . $subAccount->appAccountId;
        $method = 'PUT';
        $data = [
            'displayName' => $subAccount->displayName,
        ];

        $this->query($path, $method, $data);
        return $subAccount;
    }

    public function getSubAccount(string $appUserId, string $appAccountId): SubAccount
    {
        $path = '/users/' . $appUserId . '/subaccounts/' . $appAccountId;
        $method = 'GET';
        $res = $this->query($path, $method);

        $data = json_decode($res->getBody()->__toString(), true);
        
        $subAccountData = [
            'id' => $data['Id'],
            'appAccountId' => $data['AppAccountId'],
            'displayName' => $data['DisplayName'],
            'amount' => $data['Amount'],
        ];
        $subAccount = new SubAccount($subAccountData);

        return $subAccount;
    }

    public function verifySignature(MessageInterface $message)
    {
        parse_str($message->getBody()->__toString(), $body);

        if (array_key_exists('CallbackSignature', $body) === false) {
            throw new InvalidSignatureException('Missing signature');
        }
        $signature = $body['CallbackSignature'];
        unset($body['CallbackSignature']);

        ksort($body);
        $body[] = $this->signature;
        $hash = implode('+', array_values($body));
        if (sha1($hash) !== $signature) {
            throw new InvalidSignatureException('Invalid signature');
        }
    }

    public function retrieveKYCrequest(User $user) :KYC
    {
        $path = '/users/' . $user->appUserId . '/kyc/';
        $method = 'GET';

        $res = $this->query($path, $method);

        $data = json_decode($res->getBody()->__toString(), true);

        $kycData = [
            'id' => $data['Id'],
            'requestDate' => $data['RequestDate'],
            'status' => $data['Status'],
            'reason' => $data['Reason'],
        ];
        $kyc = new KYC($kycData);

        return $kyc;
    }
}
