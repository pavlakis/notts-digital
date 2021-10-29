<?php

namespace NottsDigital\Event;

use NottsDigital\Adapter\TitoAdapter;
use NottsDigital\Adapter\AdapterInterface;
use NottsDigital\Adapter\MeetupCrawlerAdapter;

class EventFactory
{
    /**
     * @var MeetupCrawlerAdapter|AdapterInterface
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
     *
     * @return EventInterface
     */
    public function createFromEventType(?string $eventType): EventInterface
    {
        if ('meetups' === $eventType) {
            return new Event($this->meetupAdapter);
        }

        if ('ti.to' === $eventType) {
            return new Event($this->titoAdapter);
        }

        throw new \InvalidArgumentException(sprintf('Event [%s] does not exist', $eventType));
    }
}
