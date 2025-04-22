<?php

declare(strict_types=1);

namespace Umanit\DevBundle\Test;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TestUtils::class)]
class TestUtilsTest extends TestCase
{
    public function testSetId(): void
    {
        $object = new class () {
            private int $id = 0;

            public function getId(): int
            {
                return $this->id;
            }
        };

        $this->assertEquals(0, $object->getId());
        TestUtils::setId($object, 123);
        $this->assertEquals(123, $object->getId());
    }
}
