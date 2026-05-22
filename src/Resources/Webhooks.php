<?php

declare(strict_types=1);

namespace PayIsland\Resources;

class Webhooks
{
    public function verifySignature(string $payload, string $signature, string $secret): bool
    {
        if ($signature === '' || $secret === '') {
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expectedSignature, $signature);
    }
}
