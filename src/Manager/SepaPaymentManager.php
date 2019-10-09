<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Manager;

use AssoConnect\SMoney\Client;
use AssoConnect\SMoney\Object\SepaPayment;
use Fig\Http\Message\RequestMethodInterface;

class SepaPaymentManager
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
     * Retrieve one particular SEPA payment
     * @param  string $orderId
     * @return SepaPayment
     *
     * We can't create/simulate transfer on Smoney sandbox
     * @codeCoverageIgnore
     */
    public function retrieveSepaPayment(string $orderId): SepaPayment
    {
        $path = '/payins/directdebits/' . $orderId;

        $method = RequestMethodInterface::METHOD_GET;

        $res = $this->client->query($path, $method, null, 2);

        $data = json_decode($res->getBody()->__toString(), true);

        $properties = [
            'id'     => $data['Id'],
            'status' => $data['Status'],
            'amount' => $data['Amount'],
            'paymentDate' => new \DateTime($data['PaymentDate']),
        ];

        return new SepaPayment($properties);
    }
}
