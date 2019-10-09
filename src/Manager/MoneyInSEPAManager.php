<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Manager;

use AssoConnect\SMoney\Client;
use AssoConnect\SMoney\Object\MoneyInSEPA;
use Fig\Http\Message\RequestMethodInterface;

class MoneyInSEPAManager
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
     * Retrieve one particular money in SEPA
     * @param  string $orderId
     * @return MoneyInSEPA
     *
     * We can't create/simulate transfer on Smoney sandbox
     * @codeCoverageIgnore
     */
    public function retrieveMoneyInSEPA(string $orderId): MoneyInSEPA
    {
        $path = '/payins/directdebits/' . $orderId;

        $method = RequestMethodInterface::METHOD_GET;

        $res = $this->client->query($path, $method, null, 2);

        $data = json_decode($res->getBody()->__toString(), true);

        $properties = [
            'id'     => $data['Id'],
            'status' => $data['Status'],
            'amount' => $data['Amount'],
            'paymentDate' => $data['PaymentDate'],
        ];

        return new MoneyInSEPA($properties);
    }
}
