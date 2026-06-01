<?php

declare(strict_types=1);

namespace Umanit\DevBundle\Test;

use Symfony\Component\EventDispatcher\EventDispatcher;

final class TestEventDispatcher extends EventDispatcher
{
    /** @var array<string, list<object>> */
    private array $dispatchedEvents = [];

    public function dispatch(object $event, ?string $eventName = null): object
    {
        $eventName ??= $event::class;

        $this->dispatchedEvents[$eventName][] = $event;

        return $event;
    }

    /**
     * @return array<string, list<object>>
     */
    public function getAllDispatchedEvents(): array
    {
        return $this->dispatchedEvents;
    }

    /**
     * @return list<object>
     */
    public function getDispatchedEvents(string $eventName): array
    {
        return $this->dispatchedEvents[$eventName] ?? [];
    }

    /**
     * Returns the last dispatched event of the given event name (or class name)
     * or null if none was dispatched.
     */
    public function getLastDispatchedEvent(string $eventName): ?object
    {
        $events = $this->dispatchedEvents[$eventName] ?? [];

        return [] === $events ? null : end($events);
    }
}
