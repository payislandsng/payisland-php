<?php

declare(strict_types=1);

namespace PayIsland\Tests;

use InvalidArgumentException;
use PayIsland\PayIsland;
use PHPUnit\Framework\TestCase;

class PayIslandTest extends TestCase
{
    public function testClientRequiresSecretKey(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new PayIsland([]);
    }

    public function testDefaultBaseURL(): void
    {
        $payIsland = new PayIsland([
            'secretKey' => 'sk_test',
        ]);

        $this->assertSame('https://ags.payislands.com', $payIsland->baseURL);
    }

    public function testCustomBaseURLOverrideWorks(): void
    {
        $payIsland = new PayIsland([
            'secretKey' => 'sk_test',
            'baseURL' => 'https://example.test/',
        ]);

        $this->assertSame('https://example.test', $payIsland->baseURL);
    }
}
