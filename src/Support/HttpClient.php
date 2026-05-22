<?php

declare(strict_types=1);

namespace PayIsland\Support;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use PayIsland\Exceptions\PayIslandApiException;

class HttpClient
{
    private ClientInterface $client;

    public function __construct(
        private string $secretKey,
        private string $baseURL,
        private float $timeout = 30,
        ?ClientInterface $client = null
    ) {
        $this->baseURL = rtrim($baseURL, '/');

        $this->client = $client ?? new Client([
            'base_uri' => $this->baseURL,
            'timeout' => $this->timeout,
            'http_errors' => false,
        ]);
    }

    public function get(string $path): array
    {
        return $this->request('GET', $path);
    }

    public function post(string $path, array $payload): array
    {
        return $this->request('POST', $path, $payload);
    }

    private function request(string $method, string $path, ?array $payload = null): array
    {
        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'User-Agent' => 'payisland/payisland-php',
            ],
            'http_errors' => false,
        ];

        if ($payload !== null) {
            $options['json'] = $payload;
        }

        try {
            $response = $this->client->request($method, $path, $options);
        } catch (GuzzleException $exception) {
            throw new PayIslandApiException($exception->getMessage(), 0, null, $exception);
        }

        $statusCode = $response->getStatusCode();
        $body = (string) $response->getBody();
        $data = $this->decodeResponse($body);

        if ($statusCode >= 400) {
            throw new PayIslandApiException(
                $this->extractErrorMessage($data, $statusCode),
                $statusCode,
                $data
            );
        }

        return $data ?? [];
    }

    private function decodeResponse(string $body): ?array
    {
        if ($body === '') {
            return null;
        }

        $decoded = json_decode($body, true);

        return is_array($decoded) ? $decoded : ['raw' => $body];
    }

    private function extractErrorMessage(?array $data, int $statusCode): string
    {
        if (isset($data['message']) && is_string($data['message'])) {
            return $data['message'];
        }

        if (isset($data['error']) && is_string($data['error'])) {
            return $data['error'];
        }

        return 'PayIsland API request failed with status code ' . $statusCode . '.';
    }
}
