<?php

namespace NottsDigital\Event;

use Pimple\Container;

class EventFactory
{
    /**
     * @param string    $eventType
     * @param Container $container
     * @return EventInterface
     *
     * @throws \InvalidArgumentException
     */
    public static function createFromEventType(string $eventType, Container $container): EventInterface
    {
        if (!in_array($eventType, ['ti.to', 'meetups'])) {
            throw new \InvalidArgumentException(sprintf('Event [%s] does not exist', $eventType));
        }

        return $container['event.' . $eventType];
    }
}
