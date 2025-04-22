<?php

declare(strict_types=1);

namespace Umanit\DevBundle\Foundry\Factory;

class AliasedFactoryHelper
{
    /**
     * @var array<string, class-string>
     */
    private static array $aliasedClasses = [];

    /**
     * Les classes avec alias doivent être injectées statiquement.
     * Par exemple, pendant le boot du bundle.
     *
     * @param array<string, class-string> $aliasedClasses
     */
    public static function setAliasedClasses(array $aliasedClasses): void
    {
        self::$aliasedClasses = $aliasedClasses;
    }

    /**
     * @return class-string
     */
    public static function getClass(string $alias): string
    {
        $class = self::$aliasedClasses[$alias] ?? null;
        if (null === $class) {
            throw new \RuntimeException(\sprintf('Class "%s" not found.', $alias));
        }

        return $class;
    }
}
