<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use PayIsland\PayIsland;

if (class_exists(Dotenv::class)) {
    Dotenv::createImmutable(__DIR__ . '/..')->safeLoad();
}

$secretKey = $_ENV['PAYISLAND_SECRET_KEY'] ?? getenv('PAYISLAND_SECRET_KEY');
$reference = $argv[1] ?? null;

if (!$secretKey || !$reference) {
    fwrite(STDERR, "Usage: PAYISLAND_SECRET_KEY=... php examples/verify-payment.php <reference>\n");
    exit(1);
}

$payIsland = new PayIsland([
    'secretKey' => $secretKey,
]);

$response = $payIsland->transactions->verify($reference);

echo json_encode($response, JSON_PRETTY_PRINT) . PHP_EOL;
