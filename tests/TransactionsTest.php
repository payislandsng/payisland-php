<?php

declare(strict_types=1);

namespace PayIsland\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PayIsland\Resources\Transactions;
use PayIsland\Support\HttpClient;
use PHPUnit\Framework\TestCase;

class TransactionsTest extends TestCase
{
    public function testInitializeSendsPostToInitializeEndpoint(): void
    {
        $history = [];
        $transactions = $this->transactionsWithResponses($history, [
            new Response(200, [], json_encode(['status' => true, 'data' => ['authorization_url' => 'https://pay.test']])),
        ]);

        $payload = [
            'transaction_reference' => 'order_123',
            'amount' => '1000',
        ];

        $response = $transactions->initialize($payload);

        $this->assertSame('https://pay.test', $response['data']['authorization_url']);
        $this->assertCount(1, $history);
        $this->assertSame('POST', $history[0]['request']->getMethod());
        $this->assertSame('/api/v1/transactions/in/initialize', $history[0]['request']->getUri()->getPath());
        $this->assertSame('Bearer sk_test', $history[0]['request']->getHeaderLine('Authorization'));
        $this->assertSame('application/json', $history[0]['request']->getHeaderLine('Accept'));
        $this->assertSame('payisland/payisland-php', $history[0]['request']->getHeaderLine('User-Agent'));
        $this->assertSame($payload, json_decode((string) $history[0]['request']->getBody(), true));
    }

    public function testVerifySendsGetToVerifyEndpoint(): void
    {
        $history = [];
        $transactions = $this->transactionsWithResponses($history, [
            new Response(200, [], json_encode(['status' => true, 'data' => ['reference' => 'order_123']])),
        ]);

        $response = $transactions->verify('order_123');

        $this->assertSame('order_123', $response['data']['reference']);
        $this->assertCount(1, $history);
        $this->assertSame('GET', $history[0]['request']->getMethod());
        $this->assertSame('/api/v1/transactions/in/verify/order_123', $history[0]['request']->getUri()->getPath());
    }

    private function transactionsWithResponses(array &$history, array $responses): Transactions
    {
        $mock = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(Middleware::history($history));

        $guzzle = new Client([
            'handler' => $handlerStack,
            'base_uri' => 'https://ags.payislands.com',
            'http_errors' => false,
        ]);

        $httpClient = new HttpClient('sk_test', 'https://ags.payislands.com', 30, $guzzle);

        return new Transactions($httpClient);
    }
}
