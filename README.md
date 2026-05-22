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
    'callback_url' => 'https://example.com/webhooks/payisland',
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

## Webhook Verification

```php
$isValid = $payIsland->webhooks->verifySignature(
    $rawPayload,
    $signature,
    $webhookSecret
);
```

Webhook signatures are verified with `hash_hmac('sha256', $payload, $secret)` and `hash_equals`.

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
