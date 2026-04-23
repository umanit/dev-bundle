<?php

declare(strict_types=1);

namespace Umanit\DevBundle\Foundry\Factory;

use Symfony\Contracts\Service\ResetInterface;
use Umanit\DevBundle\Foundry\Randomizer\Randomizer;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @template T of object
 *
 * @extends PersistentProxyObjectFactory<T>
 */
abstract class Factory extends PersistentProxyObjectFactory implements ResetInterface
{
    private static Randomizer $randomizer;

    protected static function getIncrementalId(): int
    {
        /** @var int $id */
        $id = FactoryStore::getValue(static::class, '__incrementalId', 0);
        FactoryStore::setValue(static::class, '__incrementalId', $id + 1);

        return $id;
    }

    protected static function addToStore(string $key, mixed $value): void
    {
        FactoryStore::append(static::class, $key, $value);
    }

    /**
     * @return list<mixed>
     */
    protected static function getListFromStore(string $key): array
    {
        return FactoryStore::getList(static::class, $key);
    }

    protected static function setInStore(string $key, mixed $value): void
    {
        FactoryStore::setValue(static::class, $key, $value);
    }

    protected static function getFromStore(string $key, mixed $default = null): mixed
    {
        return FactoryStore::getValue(static::class, $key, $default);
    }

    protected static function randomizer(): Randomizer
    {
        self::$randomizer ??= new Randomizer();

        return self::$randomizer;
    }

    public function reset(): void
    {
        FactoryStore::resetClass(static::class);
    }
}
