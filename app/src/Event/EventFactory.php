<?php

namespace NottsDigital\Event;

use NottsDigital\Adapter\MeetupAdapter;
use NottsDigital\Adapter\TitoAdapter;

class EventFactory
{
    /**
     * @var MeetupAdapter
     */
    private $meetupAdapter;

    /**
     * @var TitoAdapter
     */
    private $titoAdapter;

    public function __construct(MeetupAdapter $meetupAdapter, TitoAdapter $titoAdapter)
    {
        $this->meetupAdapter = $meetupAdapter;
        $this->titoAdapter = $titoAdapter;
    }

    /**
     * @param string $eventType|null
     * @return EventInterface
     */
    public function createFromEventType(?string $eventType): EventInterface
    {
        if (!in_array($eventType, ['ti.to', 'meetups'])) {
            throw new \InvalidArgumentException(sprintf('Event [%s] does not exist', $eventType));
        }

        if ('meetups' === $eventType) {
            return new Event($this->meetupAdapter);
        }

        if ('ti.to' === $eventType) {
            return new Event($this->titoAdapter);
        }
    }
}
