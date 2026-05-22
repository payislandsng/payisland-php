# PayIsland PHP SDK

Official PHP SDK for integrating with PayIsland payment APIs.

## Installation

```bash
composer require payisland/payisland-php
```

## Initialization

```php
use PayIsland\PayIsland;

$payIsland = new PayIsland([
    'secretKey' => getenv('PAYISLAND_SECRET_KEY'),
]);
```

You may override the API base URL and timeout when needed:

```php
$payIsland = new PayIsland([
    'secretKey' => getenv('PAYISLAND_SECRET_KEY'),
    'baseURL' => 'https://ags.payislands.com',
    'timeout' => 30,
]);
```

PayIsland determines sandbox or live mode from the API key. The SDK does not expose a separate environment flag.

## Transaction Initialization

```php
$response = $payIsland->transactions->initialize([
    'callback_url' => 'https://example.com/webhooks/payislands',
    'payment_item_id' => getenv('PAYISLAND_PAYMENT_ITEM_ID'),
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

echo $response['data']['authorization_url'];
```

## Transaction Verification

```php
$response = $payIsland->transactions->verify('order_123');
```

This calls PayIsland's documented transaction status endpoint:
`GET /api/v1/transactions/in/check-transaction-status/{reference}`.

For card transactions that require 3DS authentication, verification may return a pending status until the customer completes the challenge or PayIsland receives the final callback/webhook. Do not fulfill an order until verification returns a successful final status.

## Webhook Verification

```php
$isValid = $payIsland->webhooks->verifySignature(
    $rawPayload,
    $signature,
    $webhookSecret
);
```

Webhook signatures are verified with `hash_hmac('sha256', $payload, $secret)` and `hash_equals`.

After receiving a webhook, always verify the transaction reference before fulfillment:

```php
$payload = json_decode($rawPayload, true);
$verification = $payIsland->transactions->verify($payload['reference']);
```

If verification is still pending after a 3DS card flow, wait for the final callback/webhook or check the transaction status again before fulfilling the order.

## Error Handling

API errors throw `PayIsland\Exceptions\PayIslandApiException`.

```php
use PayIsland\Exceptions\PayIslandApiException;

try {
    $response = $payIsland->transactions->verify('order_123');
} catch (PayIslandApiException $exception) {
    echo $exception->getStatusCode();
    print_r($exception->getResponseData());
}
```

## Examples

Copy `.env.example` to `.env`, then set your PayIsland credentials.

```bash
php examples/initialize-payment.php
php examples/verify-payment.php order_123
php examples/webhook-verification.php
```

## Development

```bash
composer install
vendor/bin/phpunit
```

## License

MIT
