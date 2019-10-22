<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Manager;

use AssoConnect\SMoney\Client;
use AssoConnect\SMoney\Object\CardPayment;
use AssoConnect\SMoney\Object\CardSubPayment;
use Fig\Http\Message\RequestMethodInterface;

class CardPaymentManager
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
     * Creating card payment
     * @param $cardPayment
     * @return CardPayment
     */
    public function createCardPayment($cardPayment): CardPayment
    {
        $path = '/payins/cardpayments';
        $method = RequestMethodInterface::METHOD_POST;

        $subPaymentsTable = [];

        if ($carSubPayments = $cardPayment->cardSubPayments) {
            foreach ($carSubPayments as $cardSubPayment) {
                $subPaymentsTable[] =  [
                    'orderId' => $cardSubPayment->orderId,
                    'beneficiary' => [
                        'appaccountid' => $cardSubPayment->beneficiary['appaccountid'],
                    ],
                    'amount' => $cardSubPayment->amount,
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
    public function retrieveCardPayment($paymentOrderId): CardPayment
    {
        $path = '/payins/cardpayments/' . $paymentOrderId;
        $method = RequestMethodInterface::METHOD_GET;

        $res = $this->client->query($path, $method, null, 2);

        $data = json_decode($res->getBody()->__toString(), true);

        $properties = [
            'id'     => $data['Id'],
            'status' => $data['Status'],
            'type'   => $data['Type'],
            'amount' => $data['Amount'],
            'card'      => $data['Card'],
            'extraResults' => $data['ExtraResults'],
            'errorCode' => $data['ErrorCode'],
            'subPayments' => [],
        ];
        if (array_key_exists('Payments', $data)) {
            foreach ($data['Payments'] as $subPaymentData) {
                $subPaymentProperties = [
                    'id'            => $subPaymentData['Id'],
                    'orderId'       => $subPaymentData['OrderId'],
                    'beneficiary'   => $subPaymentData['Beneficiary'],
                    'amount'        => $subPaymentData['Amount'],
                    'status'        => $subPaymentData['Status'],
                ];
                $properties['subPayments'][] = new CardSubPayment($subPaymentProperties);
            }
        }

        $cardPayment = new CardPayment($properties);

        return $cardPayment;
    }

    /**
     * Retrieving Card sub payment's info
     * @param string $paymentOrderId
     * @param string $subPaymentOrderId
     * @return CardSubPayment
     */
    public function retrieveCardSubPayment($paymentOrderId, $subPaymentOrderId): CardSubPayment
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
}
