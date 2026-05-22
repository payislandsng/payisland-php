<?php

declare(strict_types=1);

namespace PayIsland\Resources;

use PayIsland\Support\HttpClient;

class Transactions
{
    public function __construct(private HttpClient $client)
    {
    }

    public function initialize(array $payload): array
    {
        return $this->client->post('/api/v1/transactions/in/initialize', $payload);
    }

    public function verify(string $reference): array
    {
        return $this->client->get('/api/v1/transactions/in/verify/' . rawurlencode($reference));
    }
}
