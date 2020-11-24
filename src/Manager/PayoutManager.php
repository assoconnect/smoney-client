<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Manager;

use AssoConnect\SMoney\Client;
use AssoConnect\SMoney\Exception\InvalidStringException;
use AssoConnect\SMoney\Object\Payout;
use Fig\Http\Message\RequestMethodInterface;

class PayoutManager
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }


    public function createPayout(
        string $appUserId,
        string $orderId,
        int $bankAccountId,
        string $appAccountId,
        int $amount,
        string $message,
        string $reference,
        string $motif
    ): Payout {
        $path = '/users/' . $appUserId . '/payouts/storedbankaccounts';

        $method = RequestMethodInterface::METHOD_POST;

        if (!preg_match('/^[a-zA-Z0-9 ]{0,35}$/', $reference)) {
            throw new InvalidStringException('reference');
        }

        if (!preg_match('/^[a-zA-Z0-9 ]{0,35}$/', $motif)) {
            throw new InvalidStringException('motif');
        }

        $payoutData = [
            'orderid'       => $orderId,
            'amount'        => $amount,
            'accountid'     => [
                'appaccountid'    => $appAccountId
            ],
            'bankaccount'   => [
                'id'              => $bankAccountId
            ],
            'fee'           => [
                'amountWithVAT'   => 0,
                'VAT'             => 0,
            ],
            'message'       => $message,
            'reference'     => $reference,
            'motif'         => $motif,
        ];

        $res = $this->client->query($path, $method, $payoutData, 1);
        $data = json_decode($res->getBody()->__toString(), true);

        $properties = [
            'id'     => $data['Id'],
            'orderId' => $orderId,
            'amount' => $amount,
        ];

        return new Payout($properties);
    }
}
