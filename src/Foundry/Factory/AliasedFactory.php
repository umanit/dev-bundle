<?php

declare(strict_types=1);

namespace Umanit\DevBundle\Foundry\Factory;

/**
 * @template T of object
 *
 * @extends Factory<T>
 */
abstract class AliasedFactory extends Factory
{
    abstract protected static function getEntityIdentifier(): string;

    public static function class(): string
    {
        return AliasedFactoryHelper::getClass(static::getEntityIdentifier());
    }
}
