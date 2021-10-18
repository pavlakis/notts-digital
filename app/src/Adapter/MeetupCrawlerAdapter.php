<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2017 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */
namespace NottsDigital\Adapter;

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

class MeetupCrawlerAdapter implements AdapterInterface
{
    private Client $client;
    private array $config;
    private string $baseUrl;

    private string $group;

    private ?Crawler $pageCrawler =  null;
    private string $upcomingEventUrl = '';

    public function __construct(Client $client, string $baseUrl, array $config)
    {
        $this->client = $client;
        $this->baseUrl = $baseUrl;
        $this->config = $config;
    }

    /**
     * @param string $group
     * @return void
     */
    public function fetch(string $group)
    {
        $this->group = $group;

        try {
            $crawler = $this->client->request('GET', $this->baseUrl . '/' . $this->config[$group]['url'] . '/events/' );

            $crawler = $crawler->filter('.eventList-list a');
            if (null === $crawler) {
                return;
            }
            $this->upcomingEventUrl = $crawler->link()->getUri();
            $this->pageCrawler = $this->client->click($crawler->link());
        } catch (\InvalidArgumentException $e) {

        }
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * // Tuesday, October 19, 2021
     *
     * // .eventTimeDisplay.eventDateTime--hover
     * // .eventTimeDisplay-startDate (Tuesday, October 19, 2021)
     * // .eventTimeDisplay-startDate-time (8:00 PM)
     *
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        try {
            $dateStr =  $this->pageCrawler->filter('span.eventTimeDisplay-startDate')->text();
            $timeStr =  $this->pageCrawler->filter('span.eventTimeDisplay-startDate-time')->text();
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

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->upcomingEventUrl;
    }

    /**
     * @return string
     */
    public function getGroupName(): string
    {
        return $this->group;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->pageCrawler->filter('.pageHead-headline.text--pageTitle');
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return '';
    }

    /**
     * @return array
     */
    public function getGroupInfo(): array
    {
        return [];
    }

    /**
     * @return string
     */
    public function getGroupDescription(): string
    {
        return '';
    }

    /**
     * @return string
     */
    public function getGroupPhoto(): string
    {
        return '';
    }

    /**
     * @return array
     */
    public function getEventEntityCollection(): array
    {
        return [];
    }
}