<?php

namespace App\Service\Marvel;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Service\Logger\LoggerService;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MarvelApiService
{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly string $publicKey,
        private readonly string $privateKey,
        private readonly LoggerService $logger
    ) {}

    public function marvelApiCall(string $endpoint, array $queryParams = []): ?array
    {
        $ts = time();
        $queryParams['ts'] = $ts;
        $queryParams['apikey'] = $this->publicKey;
        $queryParams['hash'] = md5($ts . $this->privateKey . $this->publicKey);

        try {
            $response = $this->client->request('GET', 'https://gateway.marvel.com:443/v1/public/' . $endpoint, [
                'query' => $queryParams,
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new HttpException($response->getStatusCode(), 'Marvel API call failed with status code ' . $response->getStatusCode());
            }

            return $response->toArray();

        } catch (\Exception $e) {
            $this->logger->logError('Error fetching data from Marvel API: ' . $e->getMessage());
            return null;
        }
    }
}
