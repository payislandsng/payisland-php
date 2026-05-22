<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use PayIsland\PayIsland;

if (class_exists(Dotenv::class)) {
    Dotenv::createImmutable(__DIR__ . '/..')->safeLoad();
}

$webhookSecret = $_ENV['PAYISLAND_WEBHOOK_SECRET'] ?? getenv('PAYISLAND_WEBHOOK_SECRET');

if (!$webhookSecret) {
    fwrite(STDERR, "PAYISLAND_WEBHOOK_SECRET is required.\n");
    exit(1);
}

$rawPayload = file_get_contents('php://input') ?: '{"event":"transaction.success"}';
$signature = $_SERVER['HTTP_X_PAYISLAND_SIGNATURE'] ?? hash_hmac('sha256', $rawPayload, $webhookSecret);

$payIsland = new PayIsland([
    'secretKey' => $_ENV['PAYISLAND_SECRET_KEY'] ?? getenv('PAYISLAND_SECRET_KEY') ?: 'example_secret_key',
]);

$isValid = $payIsland->webhooks->verifySignature($rawPayload, $signature, $webhookSecret);

echo $isValid ? "Valid signature\n" : "Invalid signature\n";
