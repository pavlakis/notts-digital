<?php

namespace NottsDigital\Event;

use NottsDigital\Adapter\AdapterInterface;
use NottsDigital\Adapter\MeetupAdapter;
use NottsDigital\Adapter\TitoAdapter;

class EventFactory
{
    /**
     * @var MeetupAdapter|AdapterInterface
     */
    private $meetupAdapter;

    /**
     * @var TitoAdapter|AdapterInterface
     */
    private $titoAdapter;

    public function __construct(AdapterInterface $meetupAdapter, AdapterInterface $titoAdapter)
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