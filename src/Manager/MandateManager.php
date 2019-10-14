<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Manager;

use AssoConnect\SMoney\Client;
use AssoConnect\SMoney\Object\MandateRequest;
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
     * Sign an electronic mandate
     * @param string $appUserId
     * @param int $bankAccountId
     * @param string $urlReturn
     * @param string $urlCallback
     * @return MandateRequest
     *
     */
    public function createMandateRequest(
        string $appUserId,
        int $bankAccountId,
        string $urlReturn,
        string $urlCallback
    ): MandateRequest {
        $path = '/users/' . $appUserId . '/mandates';
        $method = RequestMethodInterface::METHOD_POST;
        $bankAccountData = [
            'bankaccount' => ['id' => $bankAccountId],
            'urlreturn'  => $urlReturn,
            'urlcallback'  => $urlCallback,
        ];

        $res = $this->client->query($path, $method, $bankAccountData, 2);
        $data = json_decode($res->getBody()->__toString(), true);

        $mandateRequestData = [
            'id' => $data['Id'],
            'bankAccount' => [
                'id' => $bankAccountId,
                'href' => $data['BankAccount']['Href'],
            ],
            'date' => substr($data['Date'], 0, strrpos($data['Date'], '.')),
            'href' => $data['Href'],
            'status' => $data['Status'],
            'UMR' => $data['UMR'],
        ];

        return new MandateRequest($mandateRequestData);
    }

    /**
     * Retrieve a mandate
     * @param  int $id
     * @param  string $appUserId
     * @return array
     *
     */
    public function getMandate(string $appUserId, int $id): array
    {

        $path = '/users/' . $appUserId . '/mandates/' . $id;
        $method = RequestMethodInterface::METHOD_GET;

        $res = $this->client->query($path, $method);
        $data = json_decode($res->getBody()->__toString(), true);

        $bankAccountData = [
            'id' => $data['BankAccount']['Id'],
            'href' => $data['BankAccount']['Href']
        ];

        $mandateData = [
            'id' => $data['Id'],
            'bankAccount' => $bankAccountData,
            'date' => $data['Date'],
            'status' => $data['Status'],
            'UMR' => $data['UMR'],
        ];

        return [
            $mandateData,
            'mandateDemands' => isset($data['mandateDemands']) ? $data['mandateDemands'] : null,
            'errorCode' => isset($data['mandateDemands']) ? $data['ErrorCode'] : null,


        ];
    }

    /**
     * Send a paper mandate to S-money
     * @param  string $appUserId
     * @param  int $id
     * @param  UploadedFileInterface $file
     * @return Bool
     *
     */
    public function sendPaperMandate(string $appUserId, int $id, UploadedFileInterface $file): bool
    {
        $path = '/users/' . $appUserId . '/mandates/' . $id . '/attachments';
        $method = RequestMethodInterface::METHOD_POST;
        $filename = 'mandate - ' . date('Y-m-d H:i:s') . '.' . pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);

        $options = [
            'multipart' => [
                [
                    'Content-Disposition' => "form-data",
                    'name' => "mandat",
                    'filename' => $filename,
                    'contents' => $file->getStream(),
                    'headers' => [
                        'Content-Type'  => $file->getClientMediaType(),
                    ]
                ],
            ],
        ];
        $res = $this->client->query($path, $method, null, 2, $options);
        return ($res->getStatusCode() === 201);
    }
}
