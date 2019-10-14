<?php

namespace NottsDigital\tests\Event;

use NottsDigital\Event\GetEventDetails;
use NottsDigital\Event\GetEventDetailsInterface;
use PHPUnit\Framework\MockObject\MockObject;
use NottsDigital\Event\EventFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;

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

    protected function setUp()
    {
        $this->eventFactory = $this->createMock(EventFactory::class);
        $this->request = $this->createMock(ServerRequestInterface::class);
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
}
