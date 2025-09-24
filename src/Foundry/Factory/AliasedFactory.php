<?php

declare(strict_types=1);

namespace Umanit\DevBundle\Foundry\Factory;

use Zenstruck\Foundry\Persistence\Proxy;

/**
 * @template T of object
 *
 * @extends Factory<T&Proxy<T>>
 */
abstract class AliasedFactory extends Factory
{
    abstract protected static function getEntityIdentifier(): string;

    public static function class(): string
    {
        return AliasedFactoryHelper::getClass(static::getEntityIdentifier());
    }
}
