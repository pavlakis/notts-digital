<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2017 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */
namespace NottsDigital\Tests\Event;

use Goutte\Client;
use PHPUnit\Framework\TestCase;
use NottsDigital\Adapter\MeetupCrawlerAdapter;
use NottsDigital\Event\EventEntityCollection;
use NottsDigital\Event\EventEntityInterface;
use Symfony\Component\DomCrawler\Crawler;

class EventTest extends TestCase
{
    /**
     * @var MeetupCrawlerAdapter
     */
    protected $meetupAdapter;

    /**
     * @var Crawler
     */
    private $mainPageCrawler;

    /**
     * @var Crawler
     */
    private $eventsListingPageCrawler;

    /**
     * @var Crawler
     */
    private $upcomingEventPageCrawler;

    private $client;

    protected function setUp(): void
    {
        $this->mainPageCrawler = new Crawler(null, null, 'https://www.meetup.com/Notts-Techfast/');
        $this->mainPageCrawler->addHtmlContent(file_get_contents(dirname(__DIR__) . '/Adapter/feeders/NottsTechFast-main-page.meetup.html'));

        $this->eventsListingPageCrawler = new Crawler(null, null, 'https://www.meetup.com/Notts-Techfast/events/');
        $this->eventsListingPageCrawler->addHtmlContent(file_get_contents(dirname(__DIR__) . '/Adapter/feeders/NottsTechFast-events.meetup.html'));

        $this->upcomingEventPageCrawler = new Crawler(null, null, 'https://www.meetup.com/Notts-Techfast/events/281441655/');
        $this->upcomingEventPageCrawler->addHtmlContent(file_get_contents(dirname(__DIR__) . '/Adapter/feeders/NottsTechFast-upcoming-event.meetup.html'));

        $this->client = $this->createMock(Client::class);

        $this->meetupAdapter = new MeetupCrawlerAdapter(
            $this->client,
            'https://www.meetup.com',
            [
                "Notts Techfast" => [
                    "group_urlname" => "Notts-Techfast"
                ],
            ],
            new EventEntityCollection()
        );
    }

    public function testEventCanHandleMultipleEvents()
    {
        $this->client->expects($this->exactly(2))
            ->method('request')
            ->willReturnOnConsecutiveCalls(
                $this->mainPageCrawler,
                $this->eventsListingPageCrawler
            );

        $this->client->method('click')
            ->willReturn($this->upcomingEventPageCrawler);

        $meetupAdapter = new MeetupCrawlerAdapter(
            $this->client,
            'https://www.meetup.com',
            [
                "Notts Techfast" => [
                    "group_urlname" => "Notts-Techfast"
                ],
            ],
            new EventEntityCollection()
        );

        $meetupAdapter->fetch('Notts Techfast');

        /** @var EventEntityInterface $meetupCrawlerEventEntity */
        $meetupCrawlerEventEntity = $meetupAdapter->getEventEntityCollection()[0];


        $this->assertSame('Design for Developers', $meetupCrawlerEventEntity->getTitle());
        $this->assertSame('Online event', $meetupCrawlerEventEntity->getLocation());

        $eventInfo = $meetupCrawlerEventEntity->toArray();
        $this->assertSame('Thursday 4th November 2021 at 8:00am', $eventInfo['date_time']);
    }
}