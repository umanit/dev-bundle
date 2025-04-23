<?php

declare(strict_types=1);

namespace Umanit\DevBundle\Foundry\Factory;

use Umanit\DevBundle\Foundry\Randomizer\Randomizer;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;

/**
 * @template T of object
 * @extends PersistentProxyObjectFactory<T&Proxy<T>>
 */
abstract class Factory extends PersistentProxyObjectFactory
{
    private static int $incrementalId = 0;

    private static Randomizer $randomizer;

    protected static function getIncrementalId(): int
    {
        return self::$incrementalId++;
    }

    protected static function randomizer(): Randomizer
    {
        self::$randomizer ??= new Randomizer();

        return self::$randomizer;
    }
}
