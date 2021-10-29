<?php

namespace NottsDigital\tests\Event;

use PHPUnit\Framework\TestCase;
use NottsDigital\Event\EventFactory;
use NottsDigital\Event\GetEventDetails;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ServerRequestInterface;
use NottsDigital\Event\GetEventDetailsInterface;

class GetEventDetailsTest extends TestCase
{
    /**
     * @var EventFactory|MockObject
     */
    private $eventFactory;

    /**
     * @var ServerRequestInterface
     */
    private $request;

    protected function setUp(): void
    {
        $this->eventFactory = $this->createMock(EventFactory::class);
        $this->request = $this->createMock(ServerRequestInterface::class);
    }

    /**
     * @test
     */
    public function invalid_group_returns_default_response()
    {
        $eventFactory = $this->createMock(EventFactory::class);
        $getEventDetails = new GetEventDetails([], $eventFactory);

        $this->request->method('getQueryParams')->willReturn([]);
        static::assertSame(\json_encode($this->getDefaultPayload(), JSON_PRETTY_PRINT), $getEventDetails->getEvent($this->request)->getBody()->getContents());
    }

    /**
     * @test
     */
    public function get_event_with_invalid_group_returns_empty_json_response(): void
    {
        $getEventDetails = new GetEventDetails([], $this->eventFactory);

        $this->request->method('getQueryParams')->willReturn([]);
        $response = $getEventDetails->getEvent($this->request);
        static::assertSame(200, $response->getStatusCode());
        static::assertSame('*', $response->getHeader('Access-Control-Allow-Origin')[0]);
    }

    public function getEvent()
    {
        $eventFactory = $this->createMock(EventFactory::class);
        $getEventDetails = new GetEventDetails(['group' => ['name' => 'phpMinds']], $eventFactory);

        $this->request->method('getQueryParams')->willReturn([]);
        static::assertSame(\json_encode($this->getDefaultPayload(), JSON_PRETTY_PRINT), $getEventDetails->getEvent($this->request)->getBody()->getContents());
    }

    /**
     * @return array
     */
    private function getDefaultPayload(): array
    {
        return GetEventDetailsInterface::DEFAULT_PAYLOAD;
    }
}
