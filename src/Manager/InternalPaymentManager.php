<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Manager;

use AssoConnect\SMoney\Client;
use AssoConnect\SMoney\Object\InternalPayment;
use AssoConnect\SMoney\Object\SepaPayment;
use AssoConnect\SMoney\Parser\InternalPaymentParser;
use Fig\Http\Message\RequestMethodInterface;

/**
 * @link https://api.s-money.fr/documentation/utiliser-l-api/transfert-d-argent/
 */
class InternalPaymentManager
{
    protected Client $client;
    protected InternalPaymentParser $parser;

    public function __construct(Client $client, InternalPaymentParser $parser)
    {
        $this->client = $client;
        $this->parser = $parser;
    }

    /**
     * @return InternalPayment[]
     */
    public function retrieveInternalPayments(int $page = 1, int $perPage = 50): array
    {
        $path = '/payments?page=' . $page . '&perPage=' . $perPage;
        $method = RequestMethodInterface::METHOD_GET;

        $res = $this->client->query($path, $method);

        $data = json_decode($res->getBody()->__toString(), true);

        return array_map([$this->parser, 'parse'], $data);
    }
}
