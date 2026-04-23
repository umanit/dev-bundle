<?php

declare(strict_types=1);

namespace Umanit\DevBundle\Foundry\Factory;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FactoryStore::class)]
class FactoryStoreTest extends TestCase
{
    protected function tearDown(): void
    {
        FactoryStore::resetAll();
    }

    public function testAppendAndGetList(): void
    {
        FactoryStore::append('FooFactory', 'paths', '/path/1');
        FactoryStore::append('FooFactory', 'paths', '/path/2');

        $this->assertSame(['/path/1', '/path/2'], FactoryStore::getList('FooFactory', 'paths'));
    }

    public function testGetListReturnsEmptyArrayForUnknownKey(): void
    {
        $this->assertSame([], FactoryStore::getList('FooFactory', 'missing'));
    }

    public function testSetAndGetValue(): void
    {
        FactoryStore::setValue('FooFactory', 'counter', 42);

        $this->assertSame(42, FactoryStore::getValue('FooFactory', 'counter'));
    }

    public function testGetValueReturnsDefaultForUnknownKey(): void
    {
        $this->assertNull(FactoryStore::getValue('FooFactory', 'missing'));
        $this->assertSame(0, FactoryStore::getValue('FooFactory', 'missing', 0));
    }

    public function testResetClassClearsOnlyTargetClass(): void
    {
        FactoryStore::append('FooFactory', 'paths', '/path/1');
        FactoryStore::append('BarFactory', 'ids', 1);

        FactoryStore::resetClass('FooFactory');

        $this->assertSame([], FactoryStore::getList('FooFactory', 'paths'));
        $this->assertSame([1], FactoryStore::getList('BarFactory', 'ids'));
    }

    public function testResetAllClearsEveryClass(): void
    {
        FactoryStore::append('FooFactory', 'paths', '/path/1');
        FactoryStore::append('BarFactory', 'ids', 1);

        FactoryStore::resetAll();

        $this->assertSame([], FactoryStore::getList('FooFactory', 'paths'));
        $this->assertSame([], FactoryStore::getList('BarFactory', 'ids'));
    }

    public function testStoresAreIsolatedByClass(): void
    {
        FactoryStore::append('FooFactory', 'paths', '/foo');
        FactoryStore::append('BarFactory', 'paths', '/bar');

        $this->assertSame(['/foo'], FactoryStore::getList('FooFactory', 'paths'));
        $this->assertSame(['/bar'], FactoryStore::getList('BarFactory', 'paths'));
    }

    public function testAppendAccumulatesAcrossMultipleCalls(): void
    {
        for ($i = 1; $i <= 5; ++$i) {
            FactoryStore::append('FooFactory', 'ids', $i);
        }

        $this->assertSame([1, 2, 3, 4, 5], FactoryStore::getList('FooFactory', 'ids'));
    }

    public function testSetValueOverwritesPreviousValue(): void
    {
        FactoryStore::setValue('FooFactory', 'flag', false);
        FactoryStore::setValue('FooFactory', 'flag', true);

        $this->assertTrue(FactoryStore::getValue('FooFactory', 'flag'));
    }

    public function testGetListThrowsWhenKeyHoldsNonArrayValue(): void
    {
        FactoryStore::setValue('FooFactory', 'paths', 'not-an-array');

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Cannot get list for key "paths" in "FooFactory"');

        FactoryStore::getList('FooFactory', 'paths');
    }

    public function testAppendThrowsWhenKeyAlreadyHoldsNonArrayValue(): void
    {
        FactoryStore::setValue('FooFactory', 'paths', 'not-an-array');

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Cannot append to key "paths" in "FooFactory"');

        FactoryStore::append('FooFactory', 'paths', '/new');
    }
}
