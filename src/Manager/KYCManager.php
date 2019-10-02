<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Manager;

use AssoConnect\SMoney\Client;
use AssoConnect\SMoney\Object\BankAccount;
use AssoConnect\SMoney\Object\KYC;
use AssoConnect\SMoney\Object\User;
use Fig\Http\Message\RequestMethodInterface;
use Psr\Http\Message\UploadedFileInterface;

class KYCManager
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
     * Send bank details for a bank account
     * @param string $appUserId
     * @param BankAccount $bankAccount
     * @param UploadedFileInterface $bankDetails
     * @return bool
     *
     * Sandbox default value for KYC is valid, we can't test KYC validation
     * @codeCoverageIgnore
     */
    public function submitBankAccountDetails(
        string $appUserId,
        BankAccount $bankAccount,
        UploadedFileInterface $bankDetails
    ): bool {
        $path = '/users/' . $appUserId . '/bankaccounts/' . $bankAccount->id . '/rib/attachments';
        $method = RequestMethodInterface::METHOD_POST;

        $extension = pathinfo($bankDetails->getClientFilename(), PATHINFO_EXTENSION);
        $filename = $bankAccount->iban . '.' . $extension;

        $options = [
            'multipart' => [
                [
                    'name' => $bankAccount->iban,
                    'filename' => $filename,
                    'contents' => $bankDetails->getStream(),
                    'headers' => [
                        'Content-Type' => $bankDetails->getClientMediaType(),
                    ]
                ],
            ],
        ];
        $res = $this->client->query($path, $method, null, 1, $options);
        return ($res->getStatusCode() === 201);
    }

    /**
     * Submiting KYC request to verify the given User
     * @param string $appUserId
     * @param UploadedFileInterface[] $files
     * @return KYC
     */
    public function submitKYCRequest(string $appUserId, iterable $files): KYC
    {
        $path = '/users/' . $appUserId . '/kyc/';
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
                    'Content-Type' => $file->getClientMediaType(),
                ],
            ];
        }

        $res = $this->client->query($path, $method, null, 1, $options);

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
     * Retrieving KYC requests' info
     * @param string $appUserId
     * @return array
     */
    public function retrieveKYCRequests(string $appUserId): array
    {
        $path = '/users/' . $appUserId . '/kyc/';
        $method = RequestMethodInterface::METHOD_GET;

        $res = $this->client->query($path, $method);

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
}
