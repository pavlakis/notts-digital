<?php
/**
 * Nottingham Digital events.
 *
 * @see      https://github.com/pavlakis/notts-digital
 *
 * @copyright Copyright (c) 2017 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */

namespace NottsDigital\Adapter;

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

class TitoAdapter implements AdapterInterface
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var \Symfony\Component\DomCrawler\Crawler
     */
    protected $event;

    /**
     * @var string
     */
    protected $group;

    public function __construct(Client $client, string $baseUrl, array $config)
    {
        $this->client = $client;
        $this->baseUrl = $baseUrl;
        $this->config = $config;
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
            $crawler = $this->client->request('GET', $this->baseUrl.'/'.$this->config[$group]['url']);
            $this->event = $crawler->filter('.events .future > a')->eq(0);
        } catch (\InvalidArgumentException $e) {
            $this->event = new Crawler();
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
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        $dateStr = '';
        try {
            $dateStr = $this->event->text();
        } catch (\InvalidArgumentException $e) {
        }

        preg_match("/(\w+)(\s{1})(\d{1,2})([a-zA-z]{2}),\s{1}(\d{4})/", $dateStr, $date);

        if (!is_array($date) || empty($date)) {
            throw new \InvalidArgumentException('Date does not exist or format unknown.');
        }

        /** @var \DateTime $date */
        $date = \DateTime::createFromFormat('F jS\, Y', $date[0]);

        return $date;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        $url = '';
        try {
            $url = $this->getBaseUrl().$this->event->attr('href');
        } catch (\Exception $e) {
        }

        return $url;
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
        return '';
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
