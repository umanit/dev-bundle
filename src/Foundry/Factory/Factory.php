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

    /**
     * Permet de setter une valeur en mettant en place un facteur chance
     *
     * exemple:
     * $probability = 1, $inTotal = 4, $value = 'present';
     * Il y aura 1 chance sur 4 que la méthode renvoie la chaine de caractère 'présent'
     */
    protected function gambleReturnMixed(
        int $probability,
        int $inTotal,
        mixed $value = true
    ): string|bool|int|\DateTime|null {
        if (\is_string($value) || $value instanceof \DateTime) {
            return (random_int(1, $inTotal) <= $probability) ? $value : null;
        }

        if (\is_bool($value)) {
            return (random_int(1, $inTotal) <= $probability);
        }

        if (\is_numeric($value)) {
            return (random_int(1, $inTotal) <= $probability) ? random_int(0, (int) $value) : null;
        }

        if (\is_array($value) && 0 !== $inTotal) {
            // self::randomizer()->valueOrNull(self::faker()->randomElement($value))
            $randomKey = array_rand($value);

            return (random_int(1, $inTotal) <= $probability) ? $value[$randomKey] : null;
        }

        return null;
    }

    /**
     * Même chose qu'au-dessus
     * Sauf qu'ici on renvoie un tableau
     * On passe en paramètre un tableau de valeurs
     * On va recréer un tableau avec une ou plusieurs valeurs du paramètre entrant (ou toutes si spécifié)
     * Et on renvoie ce dernier tableau
     *
     * Exemple :
     * $probability = 1, $inTotal = 4, $value = ['en', 'fr', 'it'];
     * Il y aura 1 chance sur 4 que la méthode renvoie un tableau avec des valeurs
     * Et le tableau pourra être composé de ['it'], ou ['it', 'fr'] ou encore ['it', 'en'].
     * Le nombre de possibilités équivaut au nombre de combinaisons des valeurs entrantes.
     *
     * @param list<mixed> $values
     *
     * @return list<mixed>
     */
    protected function gambleReturnArray(
        int $probability,
        int $inTotal,
        array $values,
        bool $alwaysAllValues = false
    ): array {
        // Si alwaysAllValues = true -> valueOrNull
        // Sinon utiliser faker -> randomElements
        $array = [];

        foreach ($values as $item) {
            if ($alwaysAllValues || random_int(1, 3) <= 1) {
                $array[] = $item;
            }
        }

        return (random_int(1, $inTotal) <= $probability) ? $array : [];
    }

    // TODO
    // 1/ méthode statique pour récup la classe avec les méthodes de "gamble"
    // 2/ méthode statique pour avoir les static defaults -> tableau vide et utilisé dans la méthode defaults
}
