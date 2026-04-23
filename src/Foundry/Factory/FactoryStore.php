<?php

declare(strict_types=1);

namespace Umanit\DevBundle\Foundry\Factory;

/**
 * @internal
 */
final class FactoryStore
{
    /** @var array<string, array<string, mixed>> */
    private static array $data = [];

    public static function append(string $class, string $key, mixed $value): void
    {
        $existing = self::$data[$class][$key] ?? null;

        if (null !== $existing && !\is_array($existing)) {
            throw new \LogicException(
                \sprintf('Cannot append to key "%s" in "%s": a non-array value is already stored.', $key, $class),
            );
        }

        /** @var list<mixed> $current */
        $current = $existing ?? [];
        $current[] = $value;
        self::$data[$class][$key] = $current;
    }

    /**
     * @return list<mixed>
     */
    public static function getList(string $class, string $key): array
    {
        $value = self::$data[$class][$key] ?? [];

        if (!\is_array($value)) {
            throw new \LogicException(
                \sprintf('Cannot get list for key "%s" in "%s": a non-array value is stored.', $key, $class),
            );
        }

        /** @var list<mixed> */
        return $value;
    }

    public static function getValue(string $class, string $key, mixed $default = null): mixed
    {
        return self::$data[$class][$key] ?? $default;
    }

    public static function setValue(string $class, string $key, mixed $value): void
    {
        self::$data[$class][$key] = $value;
    }

    public static function resetClass(string $class): void
    {
        unset(self::$data[$class]);
    }

    public static function resetAll(): void
    {
        self::$data = [];
    }
}
