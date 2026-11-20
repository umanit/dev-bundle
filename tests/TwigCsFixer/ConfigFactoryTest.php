<?php

declare(strict_types=1);

namespace Umanit\DevBundle\TwigCsFixer;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use TwigCsFixer\Rules\File\FileExtensionRule;

#[CoversClass(ConfigFactory::class)]
class ConfigFactoryTest extends TestCase
{
    public function testCreateRegistersTheFileExtensionRule(): void
    {
        $config = ConfigFactory::create([__DIR__]);

        $fileExtensionRules = array_filter(
            $config->getRuleset()->getRules(),
            static fn(object $rule): bool => $rule instanceof FileExtensionRule,
        );

        $this->assertCount(1, $fileExtensionRules);
    }
}
