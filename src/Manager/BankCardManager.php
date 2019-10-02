<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Manager;

use AssoConnect\SMoney\Client;
use AssoConnect\SMoney\Object\BankCardRegistration;
use AssoConnect\SMoney\Object\Card;
use Fig\Http\Message\RequestMethodInterface;

class BankCardManager
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
     * Creating bank card
     * @param BankCardRegistration $bankCardRegistration
     * @return BankCardRegistration
     */
    public function createBankCardRegistration(BankCardRegistration $bankCardRegistration): BankCardRegistration
    {
        $path = '/cards/registrations';
        $method = RequestMethodInterface::METHOD_POST;

        $data = [
            'card' => [
                'appcardid' => $bankCardRegistration->card->appCardId,
                'name' => $bankCardRegistration->card->name
            ],
            'urlReturn' => $bankCardRegistration->urlReturn,
            'availableCards' => $bankCardRegistration->availableCards,
            'extraparameters' => $bankCardRegistration->extraParameters
        ];
        $res = $this->client->query($path, $method, $data, 2);

        $data = json_decode($res->getBody()->__toString(), true);
        $bankCardRegistration->status = $data['Status'];
        $bankCardRegistration->errorCode = $data['ErrorCode'];
        $bankCardRegistration->extraResults = $data['ExtraResults'];
        $bankCardRegistration->href = $data['Href'];

        return $bankCardRegistration;
    }

    /**
     * Retrieving bank card registration's info
     * @param string $appCardId
     * @return BankCardRegistration
     */
    public function retrieveBankCardRegistration($appCardId): BankCardRegistration
    {
        $path = '/cards/registrations/' . $appCardId;
        $method = RequestMethodInterface::METHOD_GET;

        $res = $this->client->query($path, $method, null, 2);

        $data = json_decode($res->getBody()->__toString(), true);

        $cardProperties = [
            'id'                => $data['Card']['Id'],
            'appCardId'         => $data['Card']['AppCardId'],
            'name'              => $data['Card']['Name'],
            'hint'              => $data['Card']['Hint'],
            'country'           => $data['Card']['Country'],
        ];

        $card = new Card($cardProperties);

        $properties = [
            'card'              => $card,
            'status'            => $data['Status'],
            'urlReturn'         => $data['UrlReturn'],
            'urlCallback'       => $data['UrlCallback'],
            'availableCards'    => $data['AvailableCards'],
            'errorCode'         => $data['ErrorCode'],
            'extraResults'      => $data['ExtraResults'],
            'href'              => $data['Href'],
            'extraParameters'   => $data['ExtraParameters'],
        ];

        $bankCardRegistration = new BankCardRegistration($properties);

        return $bankCardRegistration;
    }
}
