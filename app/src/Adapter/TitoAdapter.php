<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2016 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */
namespace NottsDigital\Adapter;

use Goutte\Client;
use NottsDigital\Adapter\AdapterInterface;
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

    public function __construct(Client $client, $baseUrl, $config)
    {
        $this->client = $client;
        $this->baseUrl = $baseUrl;
        $this->config = $config;
    }

    /**
     * @param $group
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    public function fetch($group)
    {
        $this->group    = $group;

        try {
            $crawler = $this->client->request('GET', $this->baseUrl . '/' . $this->config[$group]['url'] );
            $this->event = $crawler->filter('.events .future > a')->eq(0);
        } catch (\InvalidArgumentException $e) {
            $this->event = new Crawler();
        }
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        $dateStr = '';
        try {
            $dateStr = $this->event->text();
        } catch (\InvalidArgumentException $e) {}

        preg_match("/(\w+)(\s{1})(\d{1,2})([a-zA-z]{2}),\s{1}(\d{4})/", $dateStr, $date);

        if (!is_array($date) || empty($date)) {
            throw new \InvalidArgumentException('Date does not exist or format unknown.');
        }

        return \DateTime::createFromFormat('F jS\, Y', $date[0]);
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        $url = '';
        try {
            $url = $this->getBaseUrl() . $this->event->attr('href');
        } catch (\Exception $e) {}

        return $url;
    }

    /**
     * @return string
     */
    public function getGroupName()
    {
        return $this->group;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return '';
    }
}