<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2021 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */
namespace NottsDigital\Event;

use Symfony\Component\DomCrawler\Crawler;

final class MeetupCrawlerEventEntity implements EventEntityInterface
{
    private string $eventUrl;
    private Crawler $meetupPageCrawler;

    public function __construct(Crawler $meetupPageCrawler, string $eventUrl)
    {
        $this->eventUrl = $eventUrl;
        $this->meetupPageCrawler = $meetupPageCrawler;
    }

    public function getTitle()
    {
        return $this->meetupPageCrawler->filter('h1.pageHead-headline.text--pageTitle')->text();
    }

    public function getDescription()
    {
        return $this->meetupPageCrawler->filter('.event-description.runningText')->text();
    }

    /**
     * // Tuesday, October 19, 2021
     *
     * // .eventTimeDisplay.eventDateTime--hover
     * // .eventTimeDisplay-startDate (Tuesday, October 19, 2021)
     * // .eventTimeDisplay-startDate-time (8:00 PM)
     *
     * $this->meetupPageCrawler->filter('span.eventTimeDisplay-startDate')->html();
     * <span>Thursday, October 28, 2021</span><br><span class="eventTimeDisplay-startDate-time"><span>7:00 PM</span></span>
     *
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        try {
            $timeStr =  $this->meetupPageCrawler->filter('span.eventTimeDisplay-startDate-time')->text();
            $dateStrMixed =  $this->meetupPageCrawler->filter('span.eventTimeDisplay-startDate')->text();
            $dateStr = str_ireplace($timeStr, '', $dateStrMixed);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Could not retrieve date.');
        }

        $date = \DateTime::createFromFormat(
            'l, F j, Y g:i A',
            $dateStr. ' ' . $timeStr
        );

        if (!$date instanceof \DateTime) {
            throw new \InvalidArgumentException('Date does not exist or format unknown.');
        }

        return $date;
    }

    public function getUrl()
    {
        return $this->eventUrl;
    }

    public function getLocation()
    {
        return $this->meetupPageCrawler->filter('p.wrap--singleLine--truncate')->text();
    }

    public function toArray(): array
    {
        $date = '';
        $isoDate = '';

        if ($this->getDate() instanceof \DateTime) {
            $date = $this->getDate()->format('l jS F Y') . ' at ' . $this->getDate()->format('g:ia');
            $isoDate = $this->getDate()->format('c');
        }

        return [
            'subject' => $this->getTitle(),
            'description' => $this->getDescription(),
            'date_time' => $date,
            'location' => $this->getLocation(),
            'event_url' => $this->getUrl(),
            'iso_date' => $isoDate
        ];
    }
}