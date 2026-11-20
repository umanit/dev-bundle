<?php

declare(strict_types=1);

namespace Umanit\DevBundle\Rector\Config;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rector\Configuration\RectorConfigBuilder;

#[CoversClass(UmanitRectorConfig::class)]
class UmanitRectorConfigTest extends TestCase
{
    public function testConfigureReturnsARectorConfigBuilder(): void
    {
        $this->assertInstanceOf(RectorConfigBuilder::class, UmanitRectorConfig::configure());
    }

    public function testConfigureReturnsANewBuilderEachTime(): void
    {
        $this->assertNotSame(UmanitRectorConfig::configure(), UmanitRectorConfig::configure());
    }
}
