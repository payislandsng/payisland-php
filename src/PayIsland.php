<?php

declare(strict_types=1);

namespace PayIsland;

use InvalidArgumentException;
use PayIsland\Resources\Transactions;
use PayIsland\Resources\Webhooks;
use PayIsland\Support\HttpClient;

class PayIsland
{
    public string $baseURL;

    public Transactions $transactions;

    public Webhooks $webhooks;

    public function __construct(array $config)
    {
        $secretKey = $config['secretKey'] ?? null;

        if (!is_string($secretKey) || trim($secretKey) === '') {
            throw new InvalidArgumentException('PayIsland secretKey is required.');
        }

        $this->baseURL = rtrim((string) ($config['baseURL'] ?? 'https://ags.payislands.com'), '/');
        $timeout = (float) ($config['timeout'] ?? 30);

        $httpClient = new HttpClient($secretKey, $this->baseURL, $timeout);

        $this->transactions = new Transactions($httpClient);
        $this->webhooks = new Webhooks();
    }
}
