<?php

namespace App\Service\Marvel;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class MarvelApiService
{
    private $client;
    private $publicKey;
    private $privateKey;

    public function __construct(HttpClientInterface $client, string $publicKey, string $privateKey)
    {
        $this->client = $client;
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
    }

    public function marvelApiCall(string $endpoint, array $queryParams = []): array
    {
        $ts = time();
        $hash = md5($ts . $this->privateKey . $this->publicKey);

        $queryParams['ts'] = $ts;
        $queryParams['apikey'] = $this->publicKey;
        $queryParams['hash'] = $hash;

        $response = $this->client->request('GET', 'https://gateway.marvel.com:443/v1/public/' . $endpoint, [
            'query' => $queryParams,
        ]);

        return $response->toArray();
    }
}
