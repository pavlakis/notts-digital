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

    public function __construct(Client $client, $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * @param $group
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    public function fetch($group)
    {
        $this->baseUrl = $this->config['baseUrl'];

        try {
            $crawler = $this->client->request('GET', $this->baseUrl . '/' . $this->config[$group]['url'] );
        } catch (\InvalidArgumentException $e) {
            return new Crawler();
        }

        return $crawler->filter('.events .future > a')->eq(0);
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }
}