<?php
/**
 * Nottingham Digital events.
 *
 * @see      https://github.com/pavlakis/notts-digital
 *
 * @copyright Copyright (c) 2017 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */

namespace NottsDigital\Tests\Event;

use PHPUnit\Framework\TestCase;
use NottsDigital\Event\NullEventEntity;
use NottsDigital\Event\EventEntityCollection;

class EventEntityCollectionTest extends TestCase
{
    /**
     * @var EventEntityCollection
     */
    private $eventEntityCollection;

    protected function setUp(): void
    {
        $this->eventEntityCollection = new EventEntityCollection();
    }

    public function test_can_retrieve_event_entity_from_collection()
    {
        $this->eventEntityCollection->add(new NullEventEntity());

        static::assertInstanceOf(NullEventEntity::class, $this->eventEntityCollection[0]);
    }
}
