<?php

declare(strict_types=1);

namespace AssoConnect\SMoney;

use AssoConnect\SMoney\Exception\InvalidSignatureException;
use AssoConnect\SMoney\Exception\MissingAppUserIdException;
use AssoConnect\SMoney\Exception\MissingBicException;
use AssoConnect\SMoney\Exception\MissingIbanException;
use AssoConnect\SMoney\Exception\MissingIdException;
use AssoConnect\SMoney\Exception\UserAgeException;
use AssoConnect\SMoney\Exception\UserAlreadyExistsException;
use AssoConnect\SMoney\Exception\UserCountryException;
use AssoConnect\SMoney\Object\Address;
use AssoConnect\SMoney\Object\BankAccount;
use AssoConnect\SMoney\Object\Beneficiary;
use AssoConnect\SMoney\Object\CardPayment;
use AssoConnect\SMoney\Object\CardSubPayment;
use AssoConnect\SMoney\Object\Company;
use AssoConnect\SMoney\Object\KYC;
use AssoConnect\SMoney\Object\MoneyInTransfer;
use AssoConnect\SMoney\Object\SubAccount;
use AssoConnect\SMoney\Object\User;
use AssoConnect\SMoney\Object\UserProfile;
use Fig\Http\Message\RequestMethodInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use Koriym\HttpConstants\RequestHeader;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UploadedFileInterface;

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
     * @var string
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
     * @param  array         $options Additional options for the request
     * @return Response
     */
    protected function query(
        string $path,
        string $method,
        iterable $data = null,
        int $version = 1,
        array $options = []
    ): ResponseInterface {

        $options = array_merge([
            RequestOptions::HEADERS => [
                RequestHeader::ACCEPT        => 'application/vnd.s-money.v' . $version . '+json',
                RequestHeader::AUTHORIZATION => 'Bearer ' . $this->token,
            ]
        ], $options);

        if ($data !== null) {
            $options = array_merge_recursive($options, [
                RequestOptions::JSON => $data,
                RequestOptions::HEADERS => [
                    RequestHeader::CONTENT_TYPE => 'application/vnd.s-money.v' . $version . '+json',
                ]
            ]);
        }

        return $this->client->request($method, $this->endpoint . $path, $options);
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
        return new User($userData);
    }

    /**
     * Creating a S-Money SubAccount linked to the given User
     * @param User $user
     * @param SubAccount $subAccount
     * @return SubAccount
     */
    public function createSubAccount(User $user, SubAccount $subAccount): SubAccount
    {
        $path = '/users/' . $user->appUserId . '/subaccounts';
        $method = RequestMethodInterface::METHOD_POST;
        $data = [
            'appaccountid' => $subAccount->appAccountId,
            'displayname'  => $subAccount->displayName,
        ];

        $res = $this->query($path, $method, $data);
        $data = json_decode($res->getBody()->__toString(), true);
        $subAccount->id = $data['Id'];
        return $subAccount;
    }

    /**
     * Updating the S-Money SubAccount's display name
     * @param User $user
     * @param SubAccount $subAccount
     * @return SubAccount
     */
    public function updateSubAccount(User $user, SubAccount $subAccount): SubAccount
    {
        $path = '/users/' . $user->appUserId . '/subaccounts/' . $subAccount->appAccountId;
        $method = RequestMethodInterface::METHOD_PUT;
        $data = [
            'displayName' => $subAccount->displayName,
        ];

        $this->query($path, $method, $data);

        return $subAccount;
    }

    /**
     * Retrieving the S-Money SubAccount info
     * @param User $user
     * @param string $appAccountId
     * @return SubAccount
     */
    public function getSubAccount(User $user, string $appAccountId): SubAccount
    {
        $path = '/users/' . $user->appUserId . '/subaccounts/' . $appAccountId;
        $method = RequestMethodInterface::METHOD_GET;
        $res = $this->query($path, $method);

        $data = json_decode($res->getBody()->__toString(), true);

        $subAccountData = [
            'id' => $data['Id'],
            'appAccountId' => $data['AppAccountId'],
            'displayName' => $data['DisplayName'],
            'amount' => $data['Amount'],
        ];
        return new SubAccount($subAccountData);
    }

    /**
     * Creating a S-Money BankAccount for the given User
     * @param User $user
     * @param BankAccount $bankAccount
     * @return BankAccount
     */
    public function createBankAccount(User $user, BankAccount $bankAccount) :BankAccount
    {
        if ($bankAccount->iban === '' || $bankAccount->iban === null) {
            throw new MissingIbanException('The BankAccount must have an IBAN');
        }

        if ($bankAccount->bic === '' || $bankAccount->bic === null) {
            throw new MissingBicException('The BankAccount must have a BIC');
        }

        $path = '/users/' . $user->appUserId . '/bankaccounts';
        $method = RequestMethodInterface::METHOD_POST;
        $data = [
            'displayname' => $bankAccount->displayName,
            'bic'  => $bankAccount->bic,
            'iban'  => $bankAccount->iban,
        ];
        $res = $this->query($path, $method, $data);
        $data = json_decode($res->getBody()->__toString(), true);
        $bankAccount->id = $data['Id'];
        $bankAccount->status = $data['Status'];
        return $bankAccount;
    }

    /**
     * Retrieving the BankAccount info
     * @param User $user
     * @param BankAccount $bankAccount
     * @return BankAccount
     */
    public function getBankAccount(user $user, BankAccount $bankAccount) :BankAccount
    {
        $path = '/users/' . $user->appUserId . '/bankaccounts/' . $bankAccount->id;
        $method = RequestMethodInterface::METHOD_GET;
        $res = $this->query($path, $method);

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

        $this->query($path, $method, $data);
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

        $this->query($path, $method);
        $bankAccount->id = null;
        return true;
    }

    /**
     * Checking S-Money Callback signature is valid
     * @param MessageInterface $message
     */
    public function verifySignature(MessageInterface $message)
    {
        $body = $message->getParsedBody();

        if (array_key_exists('CallbackSignature', $body) === false) {
            throw new InvalidSignatureException('Missing signature');
        }
        $signature = $body['CallbackSignature'];
        unset($body['CallbackSignature']);

        if ($this->makeCallbackSignature($body) !== $signature) {
            throw new InvalidSignatureException('Invalid signature');
        }
    }

    /**
     * Making the S-Money Signature with the given table
     * @param iterable $table
     * @return string
     */
    private function makeCallbackSignature(iterable $table) :string
    {
        ksort($table);
        $table[] = $this->signature;
        $hash = implode('+', array_values($table));
        return sha1($hash);
    }

    /**
     * Adding the S-Money signature to the given body
     * @param iterable $body
     * @return iterable
     */
    public function signPayload(iterable $body) :iterable
    {
        $body['CallbackSignature'] = $this->makeCallbackSignature($body);

        return $body;
    }

    /**
     * Send bank details for a bank account
     * @param User $user
     * @param BankAccount $bankAccount
     * @param UploadedFileInterface $bankDetails
     * @return bool
     *
     * Sandbox default value for KYC is valid, we can't test KYC validation
     * @codeCoverageIgnore
     */
    public function submitBankAccountDetails(
        User $user,
        BankAccount $bankAccount,
        UploadedFileInterface $bankDetails
    ): bool {
        $path = '/users/' . $user->appUserId . '/bankaccounts/' . $bankAccount->id . '/rib/attachments';
        $method = RequestMethodInterface::METHOD_POST;

        $extension = pathinfo($bankDetails->getClientFilename(), PATHINFO_EXTENSION);
        $filename = $bankAccount->iban . '.' . $extension;

        $options = [
            RequestOptions::MULTIPART => [
                [
                    'name' => $bankAccount->iban,
                    'filename' => $filename,
                    'contents' => $bankDetails->getStream(),
                    'headers' => [
                        RequestHeader::CONTENT_TYPE => $bankDetails->getClientMediaType(),
                    ]
                ],
            ],
        ];
        $res = $this->query($path, $method, null, 1, $options);
        return ($res->getStatusCode() === 201);
    }

    /**
     * Submiting KYC request to verify the given User
     * @param User $user
     * @param UploadedFileInterface[] $files
     * @return KYC
     */
    public function submitKYCRequest(User $user, iterable $files) :KYC
    {
        $path = '/users/' . $user->appUserId . '/kyc/';
        $method = RequestMethodInterface::METHOD_POST;

        /**
         * @var UploadedFileInterface $file
         */
        foreach ($files as $name => $file) {
            $name = preg_replace('#[^a-zA-Z0-9]+#', '-', $name);
            $extension = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
            $filename = $name . '.' . $extension;


            $options['multipart'][] = [
                'name' => $name,
                'filename' => $filename,
                'contents' => $file->getStream(),
                'headers' => [
                    RequestHeader::CONTENT_TYPE => $file->getClientMediaType(),
                ],
            ];
        }

        $res = $this->query($path, $method, null, 1, $options);

        $data = json_decode($res->getBody()->__toString(), true);

        $kycData = [
            'id' => $data['Id'],
            'requestDate' => $data['RequestDate'],
            'status' => $data['Status'],
            'reason' => $data['Reason'],
        ];
        return new KYC($kycData);
    }

    /**
     * Retrieving KYC request's info
     * @param User $user
     * @return iterable
     */
    public function retrieveKYCRequest(User $user): iterable
    {
        $path = '/users/' . $user->appUserId . '/kyc/';
        $method = RequestMethodInterface::METHOD_GET;

        $res = $this->query($path, $method);

        $data = json_decode($res->getBody()->__toString(), true);

        $kycs = [];
        foreach ($data as $item) {
            $kycData = [
                'id' => $item['Id'],
                'requestDate' => $item['RequestDate'],
                'status' => $item['Status'],
                'reason' => $item['Reason'],
            ];
            $kycs[] = new KYC($kycData);
        }

        return $kycs;
    }

    /**
     * Retrieve one particular transfer
     * @param  string $appUserId
     * @param  $id
     * @return MoneyInTransfer
     *
     * We can't create/simulate transfer on S-Money sandbox
     * @codeCoverageIgnore
     */
    public function getMoneyInTransfer(
        string $appUserId,
        string $id
    ): MoneyInTransfer {
        $path = '/users/' . $appUserId . '/payins/banktransfers/' . $id;
        $method = RequestMethodInterface::METHOD_GET;

        $res = $this->query($path, $method);
        $data = json_decode($res->getBody()->__toString(), true);

        $beneficiaryData = [
            'id' => $data['Beneficiary']['Id'],
            'appAccountId' => $data['Beneficiary']['AppAccountId'],
            'displayName' => $data['Beneficiary']['DisplayName'],
        ];

        $moneyInData = [
            'id' => $data['Id'],
            'amount' => $data['Amount'],
            'beneficiary' => new Beneficiary($beneficiaryData),
            'status' => $data['Status'],
        ];

        return new MoneyInTransfer($moneyInData);
    }

    
    /**
     * Creating card payment
     * @param $cardPayment
     * @return CardPayment
     */
    public function createCardPayment($cardPayment) :CardPayment
    {
        $path = '/payins/cardpayments';
        $method = RequestMethodInterface::METHOD_POST;

        $subPaymentsTable = [];

        if ($carSubPayments = $cardPayment->cardSubPayments) {
            foreach ($carSubPayments as $cardSubPayment) {
                $subPaymentsTable[] =  [
                    'orderId' => $cardSubPayment->orderId,
                    'beneficiary' => [
                        'appaccountid' => $cardSubPayment->beneficiary['appaccountid'],
                    ],
                    'amount' => $cardSubPayment->amount,
                ];
            }
        }

        $data = [
            'orderId' => $cardPayment->orderId,
            'isMine' => $cardPayment->isMine,
            'Require3DS' => $cardPayment->require3DS,
            'payments' => $subPaymentsTable,
            'urlReturn' => $cardPayment->urlReturn,
            'urlCallback' => $cardPayment->urlCallback,
            'amount' => $cardPayment->amount,
        ];
        $res = $this->query($path, $method, $data, 2);

        $data = json_decode($res->getBody()->__toString(), true);
        $cardPayment->id = $data['Id'];
        $cardPayment->status = $data['Status'];

        return $cardPayment;
    }

    /**
     * Retrieving card payment's info
     * @param string $paymentOrderId
     */
    public function retrieveCardPayment($paymentOrderId) :CardPayment
    {
        $path = '/payins/cardpayments/' . $paymentOrderId;
        $method = RequestMethodInterface::METHOD_GET;

        $res = $this->query($path, $method, null, 2);

        $data = json_decode($res->getBody()->__toString(), true);

        $properties = [
            'id'     => $data['Id'],
            'status' => $data['Status'],
            'type'   => $data['Type'],
            'amount' => $data['Amount'],
            'card'      => $data['Card'],
            'extraResults' => $data['ExtraResults'],
            'errorCode' => $data['ErrorCode'],
            'subPayments' => [],
        ];
        if (array_key_exists('Payments', $data)) {
            foreach ($data['Payments'] as $subPaymentData) {
                $subPaymentProperties = [
                    'orderId'        => $subPaymentData['OrderId'],
                ];
                $properties['subPayments'][] = new CardSubPayment($subPaymentProperties);
            }
        }

        return new CardPayment($properties);
    }

    /**
     * Retrieving Card sub payment's info
     * @param string $paymentOrderId
     * @param string $subPaymentOrderId
     * @return CardSubPayment
     */
    public function retrieveCardSubPayment($paymentOrderId, $subPaymentOrderId) :CardSubPayment
    {
        $path = '/payins/cardpayments/' . $paymentOrderId . '/payments/' . $subPaymentOrderId;
        $method = RequestMethodInterface::METHOD_GET;

        $res = $this->query($path, $method, null, 2);

        $data = json_decode($res->getBody()->__toString(), true);

        $properties = [
            'id'        => $data['Id'],
            'status'    => $data['Status'],
            'amount'    => $data['Amount'],
            'card'      => $data['Card'],
            'extraResults' => $data['ExtraResults'],
        ];
        return new CardSubPayment($properties);
    }
}
