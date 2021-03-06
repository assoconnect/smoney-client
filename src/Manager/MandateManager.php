<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Manager;

use AssoConnect\SMoney\Client;
use AssoConnect\SMoney\Object\Mandate;
use AssoConnect\SMoney\Object\MandateRequest;
use Fig\Http\Message\RequestMethodInterface;
use Psr\Http\Message\UploadedFileInterface;

class MandateManager
{
    protected Client $client;

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
        string $urlReturn
    ): MandateRequest {
        $path = '/users/' . $appUserId . '/mandates';
        $method = RequestMethodInterface::METHOD_POST;
        $bankAccountData = [
            'bankaccount' => ['id' => $bankAccountId],
            'urlreturn'  => $urlReturn,
        ];

        $res = $this->client->query($path, $method, $bankAccountData, 2);
        $data = json_decode($res->getBody()->__toString(), true);

        $mandateRequestData = [
            'id' => $data['Id'],
            'bankAccount' => [
                'id' => $bankAccountId,
                'href' => $data['BankAccount']['Href'],
            ],
            'date' => new \DateTime(substr($data['Date'], 0, strrpos($data['Date'], '.'))),
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
     * @return Mandate
     */
    public function getMandate(string $appUserId, int $id): Mandate
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
            'status' => $data['Status'],
            'bankAccount' => $bankAccountData,
            'date' => new \DateTime($data['Date']),
            'UMR' => $data['UMR'],
            'mandateDemands' => isset($data['mandateDemands']) ? $data['mandateDemands'] : [],
        ];

        return new Mandate($mandateData);
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
