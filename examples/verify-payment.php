<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use PayIsland\Exceptions\PayIslandApiException;
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

try {
    $response = $payIsland->transactions->verify($reference);

    echo json_encode($response, JSON_PRETTY_PRINT) . PHP_EOL;
} catch (PayIslandApiException $exception) {
    fwrite(STDERR, 'Verification failed: ' . $exception->getMessage() . PHP_EOL);

    if ($exception->getStatusCode() > 0) {
        fwrite(STDERR, 'Status code: ' . $exception->getStatusCode() . PHP_EOL);
    }

    $responseData = $exception->getResponseData();

    if ($responseData !== null) {
        fwrite(STDERR, 'Response data:' . PHP_EOL);
        fwrite(STDERR, json_encode($responseData, JSON_PRETTY_PRINT) . PHP_EOL);
    }

    exit(1);
}
