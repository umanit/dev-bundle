<?php

declare(strict_types=1);

namespace Umanit\DevBundle\Test;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TestEventDispatcher::class)]
class TestEventDispatcherTest extends TestCase
{
    private TestEventDispatcher $dispatcher;

    protected function setUp(): void
    {
        $this->dispatcher = new TestEventDispatcher();
    }

    public function testDispatchStoresEventByClassName(): void
    {
        $event = new \stdClass();

        $this->dispatcher->dispatch($event);

        $this->assertSame(['stdClass' => [$event]], $this->dispatcher->getAllDispatchedEvents());
    }

    public function testDispatchStoresEventByExplicitName(): void
    {
        $event = new \stdClass();

        $this->dispatcher->dispatch($event, 'my.event');

        $this->assertSame(['my.event' => [$event]], $this->dispatcher->getAllDispatchedEvents());
    }

    public function testDispatchReturnsEvent(): void
    {
        $event = new \stdClass();

        $returned = $this->dispatcher->dispatch($event);

        $this->assertSame($event, $returned);
    }

    public function testDispatchAccumulatesMultipleEventsUnderSameName(): void
    {
        $first = new \stdClass();
        $second = new \stdClass();

        $this->dispatcher->dispatch($first, 'my.event');
        $this->dispatcher->dispatch($second, 'my.event');

        $this->assertSame(['my.event' => [$first, $second]], $this->dispatcher->getAllDispatchedEvents());
    }

    public function testGetDispatchedEventsReturnsEventsForGivenName(): void
    {
        $first = new \stdClass();
        $second = new \stdClass();

        $this->dispatcher->dispatch($first, 'my.event');
        $this->dispatcher->dispatch($second, 'my.event');
        $this->dispatcher->dispatch(new \stdClass(), 'other.event');

        $this->assertSame([$first, $second], $this->dispatcher->getDispatchedEvents('my.event'));
    }

    public function testGetDispatchedEventsReturnsEmptyArrayForUnknownName(): void
    {
        $this->assertSame([], $this->dispatcher->getDispatchedEvents('unknown.event'));
    }

    public function testGetLastDispatchedEventReturnsNullWhenNoneDispatched(): void
    {
        $this->assertNull($this->dispatcher->getLastDispatchedEvent('my.event'));
    }

    public function testGetLastDispatchedEventReturnsLastEvent(): void
    {
        $first = new \stdClass();
        $second = new \stdClass();

        $this->dispatcher->dispatch($first, 'my.event');
        $this->dispatcher->dispatch($second, 'my.event');

        $this->assertSame($second, $this->dispatcher->getLastDispatchedEvent('my.event'));
    }

    public function testGetLastDispatchedEventReturnsNullForUnknownEventName(): void
    {
        $this->dispatcher->dispatch(new \stdClass(), 'other.event');

        $this->assertNull($this->dispatcher->getLastDispatchedEvent('my.event'));
    }
}
