<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Manager;

use AssoConnect\SMoney\Client;
use AssoConnect\SMoney\Exception\InvalidStringException;
use AssoConnect\SMoney\Object\Payout;
use AssoConnect\SMoney\Parser\PayoutParser;
use Fig\Http\Message\RequestMethodInterface;

/**
 * @link https://api.s-money.fr/documentation/utiliser-l-api/virement-vers-un-compte-bancaire/
 */
class PayoutManager
{
    protected Client $client;
    protected PayoutParser $parser;

    public function __construct(Client $client, PayoutParser $parser)
    {
        $this->client = $client;
        $this->parser = $parser;
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

        return $this->parser->parse($data);
    }

    public function retrievePayouts(int $page = 1, int $perPage = 50): array
    {
        $path = '/payouts?page=' . $page . '&perPage=' . $perPage;
        $method = RequestMethodInterface::METHOD_GET;

        $res = $this->client->query($path, $method);

        $data = json_decode($res->getBody()->__toString(), true);

        return array_map([$this->parser, 'parse'], $data);
    }
}
