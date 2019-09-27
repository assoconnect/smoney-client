<?php

namespace AssoConnect\SMoney\Tests;

use AssoConnect\SMoney\Exception\InvalidSignatureException;
use AssoConnect\SMoney\Test\SMoneyTestCase;
use GuzzleHttp\Psr7\ServerRequest;

class ClientTest extends SMoneyTestCase
{
    public function testVerifySignatureValid()
    {
        $client = $this->getClient();

        $body = [
            'orderId' => 123456,
            'amount' => 1020,
            'CallbackSignature' => '814de6e4d24008b1764fe093026b5127cddbf6c2',
        ];
        $request = new ServerRequest('POST', 'uri');

        $client->verifySignature($request->withParsedBody($body));

        $this->expectNotToPerformAssertions();
    }

    public function testSignPayload()
    {
        $client = $this->getClient();

        $table = [
            'orderId' => '123456&',
            'amount'  => '1020&',
            ];
        $body = $client->signPayload($table);

        $this->assertTrue(isset($body['CallbackSignature']));
    }


    public function testVerifySignatureInvalid()
    {
        $client = $this->getClient();

        $body = [
            'orderId' => 123456,
            'amount' => 1020,
            'CallbackSignature' => 'invalid_signature',
        ];
        $request = new ServerRequest('POST', 'uri');

        $this->expectException(InvalidSignatureException::class);
        $this->expectExceptionMessage('Invalid signature');
        $client->verifySignature($request->withParsedBody($body));
    }

    public function testVerifySignatureMissing()
    {
        $client = $this->getClient();

        $body = [
            'orderId' => 123456,
            'amount' => 1020,
        ];
        $request = new ServerRequest('POST', 'uri');

        $this->expectException(InvalidSignatureException::class);
        $this->expectExceptionMessage('Missing signature');
        $client->verifySignature($request->withParsedBody($body));
    }
}
