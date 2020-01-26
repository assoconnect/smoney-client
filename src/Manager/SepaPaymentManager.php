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
     * Creates a SEPA payment
     * @param string $appUserId
     * @param string $orderId
     * @param string $mandateId S-Money generated mandate Id
     * @param string $appAccountId
     * @param int $amount
     * @param bool $isMine
     * @return SepaPayment
     */
    public function createSepaPayment(
        string $appUserId,
        string $orderId,
        int $mandateId,
        string $appAccountId,
        int $amount,
        bool $isMine
    ): SepaPayment {
        $path = '/users/' . $appUserId . '/payins/directdebits';

        $method = RequestMethodInterface::METHOD_POST;

        $sepaPaymentData = [
            'orderid'       => $orderId,
            'mandate'       => [
                'id'    => $mandateId
            ],
            'beneficiary'   => [
                'appaccountid'  => $appAccountId
            ],
            'amount'        => $amount,
            'ismine'        => $isMine
        ];

        $res = $this->client->query($path, $method, $sepaPaymentData, 2);
        $data = json_decode($res->getBody()->__toString(), true);

        $properties = [
            'id'     => $data['Id'],
            'orderId' => $orderId,
            'status' => $data['Status'],
            'amount' => $data['Amount'],
            'paymentDate' => new \DateTime($data['PaymentDate']),
        ];

        return new SepaPayment($properties);
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
