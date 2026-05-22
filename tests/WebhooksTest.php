<?php

declare(strict_types=1);

namespace PayIsland\Tests;

use PayIsland\Resources\Webhooks;
use PHPUnit\Framework\TestCase;

class WebhooksTest extends TestCase
{
    public function testValidWebhookSignatureReturnsTrue(): void
    {
        $payload = '{"event":"transaction.success"}';
        $secret = 'webhook_secret';
        $signature = hash_hmac('sha256', $payload, $secret);

        $this->assertTrue((new Webhooks())->verifySignature($payload, $signature, $secret));
    }

    public function testInvalidWebhookSignatureReturnsFalse(): void
    {
        $payload = '{"event":"transaction.success"}';

        $this->assertFalse((new Webhooks())->verifySignature($payload, 'invalid_signature', 'webhook_secret'));
    }
}
