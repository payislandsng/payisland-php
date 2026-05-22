<?php

declare(strict_types=1);

namespace PayIsland\Exceptions;

use Exception;
use Throwable;

class PayIslandApiException extends Exception
{
    protected int $statusCode;

    protected ?array $responseData;

    public function __construct(
        string $message,
        int $statusCode = 0,
        ?array $responseData = null,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $statusCode, $previous);

        $this->statusCode = $statusCode;
        $this->responseData = $responseData;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getResponseData(): ?array
    {
        return $this->responseData;
    }
}
