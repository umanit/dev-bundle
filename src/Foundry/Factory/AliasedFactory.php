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

    /**
     * @return class-string<T>
     */
    public static function class(): string
    {
        /** @var class-string<T> $class */
        $class = AliasedFactoryHelper::getClass(static::getEntityIdentifier());

        return $class;
    }
}
