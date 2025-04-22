<?php

declare(strict_types=1);

namespace Umanit\DevBundle\Test;

final readonly class TestUtils
{
    public static function setId(object $object, int $id): void
    {
        $reflectionObject = new \ReflectionObject($object);
        $idProperty = $reflectionObject->getProperty('id');

        $idProperty->setValue($object, $id);
    }
}
