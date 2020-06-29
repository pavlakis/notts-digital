<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2017 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */
namespace NottsDigital\Tests\Event;

use NottsDigital\Event\EventEntityCollection;
use NottsDigital\Event\NullEventEntity;
use PHPUnit\Framework\TestCase;

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

    public function testCanRetrieveEventEntityFromCollection()
    {
        $this->eventEntityCollection->add(new NullEventEntity());

        static::assertInstanceOf(NullEventEntity::class, $this->eventEntityCollection[0]);
    }
}