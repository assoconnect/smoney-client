<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Manager;

use AssoConnect\SMoney\Client;
use AssoConnect\SMoney\Object\MandateRequest;
use AssoConnect\SMoney\Object\User;
use Fig\Http\Message\RequestMethodInterface;
use Psr\Http\Message\UploadedFileInterface;

class MandateManager
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
     * Creating a S-Money MandateRequest for the given User
     * @param User $user
     * @param int $bankAccountId
     * @param string $urlReturn
     * @param string $urlCallback
     * @return MandateRequest
     */
    public function createMandateRequest(User $user, int $bankAccountId, string $urlReturn, string $urlCallback) :MandateRequest
    {
        $path = '/users/' . $user->appUserId . '/mandates';
        $method = 'POST';
        $data = [
            'bankaccount' => ['id' => $bankAccountId],
            'urlreturn'  => $urlReturn,
            'urlcallback'  => $urlCallback,
        ];
        $res = $this->client->query($path, $method, $data, 2);
        $data = json_decode($res->getBody()->__toString(), true);

        $mandateRequestData = [
            'id' => $data['Id'],
            'BankAccount' => [
                'Id' => $bankAccountId,
            ],
            'date' => $data['Date'],
            'href' => $data['Href'],
            'status' => $data['Status'],
            'UMR' => $data['UMR'],
        ];

        $mandateRequest = new MandateRequest($mandateRequestData);
        return $mandateRequest;
    }

    /**
     * Retrieve one mandate
     * @param  $id
     * @param  $appUserId
     * @return MandateRequest
     *
     */
    public function getMandate(string $appUserId, int $id) :MandateRequest
    {

        $path = '/users/' . $appUserId . '/mandates/' . $id;
        $method = 'GET';

        $res = $this->client->query($path, $method);
        $data = json_decode($res->getBody()->__toString(), true);

        $bankAccountData = [
            'id' => $data['BankAccount']['Id']
        ];

        $mandateData = [
            'id' => $data['Id'],
            'href' => $data['BankAccount']['Href'],
            'bankAccount' => $bankAccountData,
            'status' => $data['Status'],
            'UMR' => $data['UMR'],
            'date' => $data['Date'],
        ];

        return new MandateRequest($mandateData);
    }

    /**
     * Retrieve one mandate
     * @param  $appUserId
     * @param  $id
     * @param  $file
     * @return Bool
     *
     */
    public function sendPaperMandate(string $appUserId, int $id, UploadedFileInterface $file) :bool
    {
        $path = '/users/' . $appUserId . '/mandates/' . $id . '/attachments';
        $method = RequestMethodInterface::METHOD_POST;
        $filename = 'mandat - ' . date('Y-m-d H:i:s') . '.' . pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);

        $options = [
            'multipart' => [
                [
                    'Content-Disposition' => "form-data",
                    'name' => "mandat",
                    'filename' => $filename,
                    'contents' => $file->getStream(),
                    'headers' => [
                        'Accept'		=> 'application/vnd.s-money.v2+json',
                        'Content-Type'	=> $file->getClientMediaType(),
                    ]
                ],
            ],
        ];
        $res = $this->client->query($path, $method, null, 2, $options);
        return ($res->getStatusCode() === 201);
    }
}
