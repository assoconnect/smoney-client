<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Manager;

use AssoConnect\SMoney\Client;
use AssoConnect\SMoney\Object\CardPayment;
use AssoConnect\SMoney\Parser\CardPaymentParser;
use Fig\Http\Message\RequestMethodInterface;

class StoredCardPaymentManager
{
    protected Client $client;
    protected CardPaymentParser $parser;

    public function __construct(Client $client, CardPaymentParser $parser)
    {
        $this->client = $client;
        $this->parser = $parser;
    }

    /**
     * @return CardPayment[]
     */
    public function retrieveStoredCardPayments(int $page = 1, int $perPage = 50): array
    {
        $path = '/payins/storedcardpayments?page=' . $page . '&perPage=' . $perPage;
        $method = RequestMethodInterface::METHOD_GET;

        $res = $this->client->query($path, $method);

        $data = json_decode($res->getBody()->__toString(), true);

        return array_map([$this->parser, 'parse'], $data);
    }
}
