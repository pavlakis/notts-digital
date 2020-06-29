<?php

namespace NottsDigital\tests\Http\Request;

use DMS\Service\Meetup\Response\MultiResultResponse;
use NottsDigital\Http\Request\MeetupRequest,
    PHPUnit\Framework\MockObject\MockObject,
    NottsDigital\Http\Client\MeetupClient,
    PHPUnit\Framework\TestCase,
    NottsDigital\Cache\Cache,
    Psr\Log\LoggerInterface;

class MeetupRequestTest extends TestCase
{
    /**
     * @var MeetupClient|MockObject
     */
    private $httpClient;

    /**
     * @var Cache|MockObject
     */
    private $cache;

    /**
     * @var LoggerInterface|MockObject
     */
    private $log;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(MeetupClient::class);
        $this->cache = $this->createMock(Cache::class);
        $this->log = $this->createMock(LoggerInterface::class);
    }

    /**
     * @test
     */
    public function fetch_event_info_from_cache(): void
    {
        $groupUrlName = 'PHPMinds';
        $this->cache->method('contains')->willReturn(true);
        $this->cache->method('fetch')->willReturn('{}');

        $meetupRequest = new MeetupRequest($this->httpClient, $this->cache, $this->log);

        static::assertSame([], $meetupRequest->fetchEventInfo($groupUrlName));
    }

    /**
     * @test
     */
    public function fetch_event_info_cache_it_and_return_from_cache(): void
    {
        $groupUrlName = 'PHPMinds';
        $multiResultResponse = $this->createMock(MultiResultResponse::class);
        $this->httpClient->method('__call')->willReturn($multiResultResponse);
        $multiResultResponse->method('json')->willReturn([]);
        $this->cache->method('contains')->willReturn(false);
        $this->cache->method('fetch')->willReturn('{}');

        $meetupRequest = new MeetupRequest($this->httpClient, $this->cache, $this->log);

        static::assertSame([], $meetupRequest->fetchEventInfo($groupUrlName));
    }

    /**
     * @test
     */
    public function fetch_event_info_catches_and_logs_exception(): void
    {
        $groupUrlName = 'PHPMinds';
        $this->httpClient->method('__call')->willThrowException(new \Exception());
        $this->log->method('alert');
        $this->cache->method('contains')->willReturn(false);
        $this->cache->method('fetch')->willReturn('{}');

        $meetupRequest = new MeetupRequest($this->httpClient, $this->cache, $this->log);

        static::assertSame([], $meetupRequest->fetchEventInfo($groupUrlName));
    }

    /**
     * @test
     */
    public function fetch_group_info_from_cache(): void
    {
        $groupUrlName = 'PHPMinds';
        $this->cache->method('contains')->willReturn(true);
        $this->cache->method('fetch')->willReturn('{}');

        $meetupRequest = new MeetupRequest($this->httpClient, $this->cache, $this->log);

        static::assertSame([], $meetupRequest->fetchGroupInfo($groupUrlName));
    }

    /**
     * @test
     */
    public function fetch_group_info_cache_it_and_return_from_cache(): void
    {
        $groupUrlName = 'PHPMinds';
        $multiResultResponse = $this->createMock(MultiResultResponse::class);
        $this->httpClient->method('__call')->willReturn($multiResultResponse);
        $multiResultResponse->method('json')->willReturn([]);
        $this->cache->method('contains')->willReturn(false);
        $this->cache->method('fetch')->willReturn('{}');

        $meetupRequest = new MeetupRequest($this->httpClient, $this->cache, $this->log);

        static::assertSame([], $meetupRequest->fetchGroupInfo($groupUrlName));
    }

    /**
     * @test
     */
    public function fetch_group_info_catches_and_logs_exception(): void
    {
        $groupUrlName = 'PHPMinds';
        $this->httpClient->method('__call')->willThrowException(new \Exception());
        $this->log->method('alert');
        $this->cache->method('contains')->willReturn(false);
        $this->cache->method('fetch')->willReturn('{}');

        $meetupRequest = new MeetupRequest($this->httpClient, $this->cache, $this->log);

        static::assertSame([], $meetupRequest->fetchGroupInfo($groupUrlName));
    }
}
