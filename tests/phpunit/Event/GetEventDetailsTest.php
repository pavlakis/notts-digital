<?php

namespace NottsDigital\tests\Event;

use NottsDigital\Event\GetEventDetails;
use NottsDigital\Event\EventFactory;
use PHPUnit\Framework\TestCase;

class GetEventDetailsTest extends TestCase
{
    /**
     * @test
     */
    public function invalid_group_returns_default_response()
    {
        $eventFactory = $this->createMock(EventFactory::class);
        $getEventDetails = new GetEventDetails([], $eventFactory);

        static::assertSame(\json_encode($this->getDefaultPayload(), JSON_PRETTY_PRINT), $getEventDetails->getEvent([])->getBody()->getContents());
    }

    /**
     * @return array
     */
    private function getDefaultPayload(): array
    {
        return [
            'group'     => '',
            'subject'   => '',
            'description'   => '',
            'date_time' => '',
            'location'  => '',
            'event_url' => '',
            'iso_date' => '',
            'next_event' => []
        ];
    }
}
