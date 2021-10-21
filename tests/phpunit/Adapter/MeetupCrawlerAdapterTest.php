<?php

declare(strict_types=1);

namespace NottsDigital\Tests\Adapter;

use Goutte\Client;
use NottsDigital\Event\EventEntityCollection;
use NottsDigital\Event\MeetupCrawlerEventEntity;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;
use NottsDigital\Adapter\MeetupCrawlerAdapter;
use Symfony\Component\DomCrawler\Link;

final class MeetupCrawlerAdapterTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_retrieve_upcoming_meetup(): void
    {
        $client = $this->createMock(Client::class);
        $crawler = $this->createMock(Crawler::class);
        $link = $this->createMock(Link::class);
        $link->method('getUri')->willReturn('https://www.meetup.com/events/group-name');

        $crawler->method('filter')->willReturnSelf();
        $crawler->method('link')->willReturn($link);

        $client->method('click')->willReturn($crawler);
        $client->method('request')->willReturn($crawler);

        $meetupCrawlerAdapter = new MeetupCrawlerAdapter(
            $client,
            'https://www.meetup.com',
            [
                "PHPMinds" => [
                    "group_urlname" => "PHPMiNDS-in-Nottingham"
                ],
            ],
            new EventEntityCollection()
        );

        $meetupCrawlerAdapter->fetch('PHPMinds');

        $this->assertInstanceOf(
            MeetupCrawlerEventEntity::class,
            $meetupCrawlerAdapter->getEventEntityCollection()[0]
        );
    }
}
