<?php

declare(strict_types=1);

namespace Umanit\DevBundle\Foundry\Randomizer;

final class Randomizer
{
    private float $defaultNullProbability = 1 / 8;

    public function setDefaultNullProbability(float $defaultNullProbability): void
    {
        $this->validateProbability($defaultNullProbability);
        $this->defaultNullProbability = $defaultNullProbability;
    }

    /**
     * @template V
     *
     * @param V $value
     *
     * @return V|null
     */
    public function valueOrNull(mixed $value, ?float $nullProbability = null): mixed
    {
        if (null !== $nullProbability) {
            $this->validateProbability($nullProbability);
        }

        $nullProbability ??= $this->defaultNullProbability;
        $randomFloat = mt_rand() / (mt_getrandmax() + 1);

        return $randomFloat < $nullProbability ? null : $value;
    }

    private function validateProbability(float $probability): void
    {
        if ($probability < 0 || $probability > 1) {
            throw new \InvalidArgumentException('Probability must be between 0 and 1');
        }
    }
}
