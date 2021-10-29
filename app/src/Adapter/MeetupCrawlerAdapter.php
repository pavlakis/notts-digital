<?php
/**
 * Nottingham Digital events.
 *
 * @see      https://github.com/pavlakis/notts-digital
 *
 * @copyright Copyright (c) 2021 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */

namespace NottsDigital\Adapter;

use Goutte\Client;
use NottsDigital\Event\EventEntity;
use NottsDigital\Event\NullEventEntity;
use Symfony\Component\DomCrawler\Crawler;
use NottsDigital\Event\EventEntityCollection;
use NottsDigital\Event\MeetupCrawlerEventEntity;

class MeetupCrawlerAdapter implements AdapterInterface
{
    private Client $client;
    private array $config;
    private string $baseUrl;

    private string $group;

    private ?Crawler $pageCrawler = null;
    private string $meetupGroupUrl = '';
    private string $upcomingEventUrl = '';

    private ?Crawler $meetupMainPage = null;

    /**
     * @var EventEntityCollection<EventEntity|NullEventEntity>
     */
    private $eventEntityCollection;

    /**
     * @param Client                                             $client
     * @param string                                             $baseUrl
     * @param array                                              $config
     * @param EventEntityCollection<EventEntity|NullEventEntity> $eventEntityCollection
     */
    public function __construct(
        Client $client,
        string $baseUrl,
        array $config,
        EventEntityCollection $eventEntityCollection
    ) {
        $this->client = $client;
        $this->baseUrl = $baseUrl;
        $this->config = $config;
        $this->eventEntityCollection = $eventEntityCollection;
    }

    /**
     * @param string $group
     *
     * @return void
     */
    public function fetch(string $group)
    {
        $this->group = $group;

        try {
            $this->meetupGroupUrl = $this->baseUrl.'/'.$this->config[$group]['group_urlname'];
            $this->meetupMainPage = $this->client->request('GET', $this->meetupGroupUrl);
            $crawler = $this->client->request('GET', $this->meetupGroupUrl.'/events/');

            $crawler = $crawler->filter('.eventList-list a');
            if (!$crawler instanceof Crawler) {
                return;
            }
            $this->upcomingEventUrl = $crawler->link()->getUri();
            $this->pageCrawler = $this->client->click($crawler->link());

            if ($this->pageCrawler instanceof Crawler) {
                $this->eventEntityCollection->add(
                    new MeetupCrawlerEventEntity($this->pageCrawler, $this->upcomingEventUrl)
                );
            }
        } catch (\InvalidArgumentException $e) {
        }
    }

    /**
     * @return string
     */
    public function getGroupName(): string
    {
        return $this->meetupMainPage->filter('a.groupHomeHeader-groupNameLink')->text();
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
        if (!$this->meetupMainPage instanceof Crawler) {
            return '';
        }

        $pageDescription = $this->meetupMainPage->filter('div.group-description--wrapper');

        return $pageDescription->filter('p.group-description')->getNode(1)->textContent;
    }

    /**
     * @return string
     */
    public function getGroupPhoto(): string
    {
        return '';
    }

    /**
     * @return \Iterator|array
     */
    public function getEventEntityCollection()
    {
        if (!$this->pageCrawler instanceof Crawler) {
            return [];
        }

        return $this->eventEntityCollection;
    }
}
