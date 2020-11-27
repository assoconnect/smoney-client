<?php

declare(strict_types=1);

namespace AssoConnect\SMoney;

use AssoConnect\SMoney\Exception\InvalidSignatureException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use Koriym\HttpConstants\RequestHeader;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;

class Client
{
    /** Guzzle Client */
    protected ClientInterface $client;

    /** S-Money endpoint */
    protected string $endpoint;

    /** Client's S-Money token */
    protected string $token;

    /** S-Money signature */
    protected string $signature;

    public function __construct(string $endpoint, string $token, ClientInterface $client, string $signature)
    {
        $this->client = $client;
        $this->endpoint = $endpoint;
        $this->token = $token;
        $this->signature = $signature;
    }

    /**
     * Queries to the S-Money API
     *
     * @param  string        $path    Endpoint path
     * @param  string        $method  Request method
     * @param  iterable|null $data    Query data
     * @param  int           $version API Version
     * @param  array         $options Additional options for the request
     * @return Response
     */
    public function query(
        string $path,
        string $method,
        iterable $data = null,
        int $version = 1,
        array $options = []
    ): ResponseInterface {

        $options = array_merge([
            RequestOptions::HEADERS => [
                RequestHeader::ACCEPT        => 'application/vnd.s-money.v' . $version . '+json',
                RequestHeader::AUTHORIZATION => 'Bearer ' . $this->token,
            ]
        ], $options);

        if ($data !== null) {
            $options = array_merge_recursive($options, [
                RequestOptions::JSON => $data,
                RequestOptions::HEADERS => [
                    RequestHeader::CONTENT_TYPE => 'application/vnd.s-money.v' . $version . '+json',
                ]
            ]);
        }

        return $this->client->request($method, $this->endpoint . $path, $options);
    }

    /**
     * Checking S-Money Callback signature is valid
     * @param MessageInterface $message
     */
    public function verifySignature(MessageInterface $message)
    {
        $body = $message->getParsedBody();

        if (array_key_exists('CallbackSignature', $body) === false) {
            throw new InvalidSignatureException('Missing signature');
        }
        $actualSignature = $body['CallbackSignature'];
        unset($body['CallbackSignature']);

        if ($this->makeCallbackSignature($body) !== $actualSignature) {
            throw new InvalidSignatureException('Invalid signature');
        }
    }

    /**
     * Making the S-Money Signature with the given table
     * @param array $table
     * @return string
     */
    private function makeCallbackSignature(array $table): string
    {
        ksort($table);
        $table[] = $this->signature;
        $hash = implode('+', array_values($table));
        return sha1($hash);
    }

    /**
     * Adding the S-Money signature to the given body
     * @param array $body
     * @return array
     */
    public function signPayload(array $body): array
    {
        $body['CallbackSignature'] = $this->makeCallbackSignature($body);

        return $body;
    }
}
