<?php

declare(strict_types=1);

namespace Umanit\DevBundle\Rector\Config;

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\Configuration\RectorConfigBuilder;
use Rector\Php74\Rector\If_\IfToNullCoalescingAssignRector;
use Rector\Symfony\CodeQuality\Rector\Class_\EventSubscriberToAttributeRector;
use Rector\Symfony\Symfony34\Rector\Closure\ContainerGetNameToTypeInTestsRector;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromAssignsRector;
use Rector\ValueObject\PhpVersion;
use Umanit\DevBundle\Rector\Rules\MonologExceptionContextKeyRector;

/**
 * Standard UmanIT Rector configuration, to use from a project's rector.php.
 *
 * The returned builder is the one of Rector: the project still declares its own paths and can add or skip any rule.
 *
 * ```php
 * return UmanitRectorConfig::configure()
 *     ->withPaths([__DIR__ . '/src', __DIR__ . '/tests'])
 *     ->withSymfonyContainerXml(__DIR__ . '/var/cache/dev/App_KernelDevDebugContainer.xml')
 * ;
 * ```
 */
final class UmanitRectorConfig
{
    private function __construct()
    {
    }

    public static function configure(): RectorConfigBuilder
    {
        $config = RectorConfig::configure();

        return $config
            ->withPhpVersion(PhpVersion::PHP_84)
            ->withPreparedSets(deadCode: true, instanceOf: true, phpunitCodeQuality: true, symfonyCodeQuality: true)
            ->withComposerBased(twig: true, doctrine: true, phpunit: true, symfony: true)
            ->withPhpSets(php84: true)
            ->withAttributesSets(all: true)
            ->withImportNames(importShortClasses: false)
            ->withRules([
                InlineConstructorDefaultToPropertyRector::class,
                MonologExceptionContextKeyRector::class,
            ])
            ->withConfiguredRule(TypedPropertyFromAssignsRector::class, [
                'inline_public' => true,
            ])
            ->withSkip([
                // Keep Event Subscribers rather than converting them to #[AsEventListener]
                EventSubscriberToAttributeRector::class,
                // Allow retrieving a service by its name from the container in tests
                ContainerGetNameToTypeInTestsRector::class,
                // Do not force the null coalescing assignment on "isset"/"if null" checks
                IfToNullCoalescingAssignRector::class,
            ])
        ;
    }
}
