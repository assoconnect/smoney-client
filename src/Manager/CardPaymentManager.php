<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Manager;

use AssoConnect\SMoney\Client;
use AssoConnect\SMoney\Object\CardPayment;
use AssoConnect\SMoney\Object\CardSubPayment;
use AssoConnect\SMoney\Parser\CardPaymentParser;
use Fig\Http\Message\RequestMethodInterface;

/**
 * @link https://api.s-money.fr/documentation/utiliser-l-api/paiement-par-carte-bancaire/
 */
class CardPaymentManager
{
    protected Client $client;
    protected CardPaymentParser $parser;

    public function __construct(Client $client, CardPaymentParser $parser)
    {
        $this->client = $client;
        $this->parser = $parser;
    }

    /**
     * Creating card payment
     * @param $cardPayment
     * @return CardPayment
     */
    public function createCardPayment(CardPayment $cardPayment): CardPayment
    {
        $path = '/payins/cardpayments';
        $method = RequestMethodInterface::METHOD_POST;

        $subPaymentsTable = [];

        if ($subPayments = $cardPayment->subPayments) {
            foreach ($subPayments as $subPayment) {
                $subPaymentsTable[] =  [
                    'orderId' => $subPayment->orderId,
                    'beneficiary' => [
                        'appaccountid' => $subPayment->beneficiary['appaccountid'],
                    ],
                    'amount' => $subPayment->amount,
                ];
            }
        }

        $data = [
            'orderId' => $cardPayment->orderId,
            'isMine' => $cardPayment->isMine,
            'Require3DS' => $cardPayment->require3DS,
            'payments' => $subPaymentsTable,
            'urlReturn' => $cardPayment->urlReturn,
            'urlCallback' => $cardPayment->urlCallback,
            'amount' => $cardPayment->amount,
        ];

        $res = $this->client->query($path, $method, $data, 2);

        $data = json_decode($res->getBody()->__toString(), true);
        $cardPayment->id = $data['Id'];
        $cardPayment->status = $data['Status'];

        return $cardPayment;
    }

    /**
     * Retrieving card payment's info
     * @param string $paymentOrderId
     */
    public function retrieveCardPayment(string $paymentOrderId): CardPayment
    {
        $path = '/payins/cardpayments/' . $paymentOrderId;
        $method = RequestMethodInterface::METHOD_GET;

        $res = $this->client->query($path, $method, null, 2);

        $data = json_decode($res->getBody()->__toString(), true);

        return $this->parser->parse($data);
    }

    /**
     * Retrieving Card sub payment's info
     * @param string $paymentOrderId
     * @param string $subPaymentOrderId
     * @return CardSubPayment
     */
    public function retrieveCardSubPayment(string $paymentOrderId, string $subPaymentOrderId): CardSubPayment
    {
        $path = '/payins/cardpayments/' . $paymentOrderId . '/payments/' . $subPaymentOrderId;
        $method = RequestMethodInterface::METHOD_GET;

        $res = $this->client->query($path, $method, null, 2);

        $data = json_decode($res->getBody()->__toString(), true);

        $properties = [
            'id'        => $data['Id'],
            'status'    => $data['Status'],
            'amount'    => $data['Amount'],
            'card'      => $data['Card'],
            'extraResults' => $data['ExtraResults'],
        ];
        return new CardSubPayment($properties);
    }

    /**
     * @return CardPayment[]
     */
    public function retrieveCardPayments(int $page = 1, int $perPage = 50): array
    {
        $path = '/payins/cardpayments?page=' . $page . '&perPage=' . $perPage;
        $method = RequestMethodInterface::METHOD_GET;

        $res = $this->client->query($path, $method, null);

        $data = json_decode($res->getBody()->__toString(), true);

        return array_map([$this->parser, 'parse'], $data);
    }
}
