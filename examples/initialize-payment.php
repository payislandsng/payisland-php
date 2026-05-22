<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use PayIsland\PayIsland;

if (class_exists(Dotenv::class)) {
    Dotenv::createImmutable(__DIR__ . '/..')->safeLoad();
}

$secretKey = $_ENV['PAYISLAND_SECRET_KEY'] ?? getenv('PAYISLAND_SECRET_KEY');
$paymentItemId = $_ENV['PAYISLAND_PAYMENT_ITEM_ID'] ?? getenv('PAYISLAND_PAYMENT_ITEM_ID');

if (!$secretKey || !$paymentItemId) {
    fwrite(STDERR, "PAYISLAND_SECRET_KEY and PAYISLAND_PAYMENT_ITEM_ID are required.\n");
    exit(1);
}

$payIsland = new PayIsland([
    'secretKey' => $secretKey,
]);

$response = $payIsland->transactions->initialize([
    'callback_url' => 'https://example.com/webhooks/payislands',
    'payment_item_id' => $paymentItemId,
    'transaction_reference' => 'order_' . time(),
    'channel' => 'card',
    'amount' => '1000',
    'customer_info' => [
        'email' => 'ada@example.com',
        'phone_number' => '08011112222',
        'first_name' => 'Ada',
        'last_name' => 'Lovelace',
    ],
]);

echo $response['data']['authorization_url'] . PHP_EOL;
