<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Manager;

use AssoConnect\SMoney\Client;
use AssoConnect\SMoney\Object\Beneficiary;
use AssoConnect\SMoney\Object\MoneyInTransfer;
use Fig\Http\Message\RequestMethodInterface;

class TransferPaymentManager
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Retrieve one particular transfer
     * @param  string $appUserId
     * @param  $id
     * @return MoneyInTransfer
     *
     * We can't create/simulate transfer on Smoney sandbox
     * @codeCoverageIgnore
     */
    public function getTransferPayment(
        string $appUserId,
        string $id
    ): MoneyInTransfer {
        $path = '/users/' . $appUserId . '/payins/banktransfers/' . $id;
        $method = RequestMethodInterface::METHOD_GET;

        $res = $this->client->query($path, $method);
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
}
