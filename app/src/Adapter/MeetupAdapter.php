<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2016 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */
namespace NottsDigital\Adapter;

use GuzzleHttp\Client;
use NottsDigital\Adapter\AdapterInterface;

class MeetupAdapter implements AdapterInterface
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var array
     */
    protected $event = [];

    /**
     * @var array
     */
    protected $groupConfig = [];

    public function __construct(Client $client, $apiKey, $baseUrl, $config)
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
        $this->baseUrl = $baseUrl;
        $this->config = $config;
    }

    /**
     * @param string $group
     * @return array
     */
    public function fetch($group)
    {
        if (!isset($this->config[$group])) {
            return [];
        }

        $groupUrlName = $this->config[$group]['group_urlname'];
        $response = $this->client->get(sprintf($this->baseUrl, $groupUrlName, $this->apiKey));
        $events = json_decode($response->getBody()->getContents(), true);

        if (isset($events['results']) && !empty($events['results'])) {
            if (isset($this->config[$group]['match'])) {
                $this->event = $this->getByNameStringMatch($events['results'], $this->config[$group]['match']);
            }

            $this->event = $events['results'][0];
            $this->groupConfig = $this->config[$group];
        }
    }

    /**
     * @param $events
     * @param $nameMatch
     * @return bool
     */
    protected function getByNameStringMatch($events, $nameMatch)
    {
        foreach ($events as $event) {
            if (strpos($event['name'], $nameMatch) !== false) {
                return $event;
            }
        }
        return [];
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        if (!isset($this->event['name'])) {
            return '';
        }

        return $this->event['name'];
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        $time = $this->event['time'] / 1000;

        $date = new \DateTime();
        $date->setTimestamp($time);

        return $date;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        if (isset($this->groupConfig['link_to'])) {
            return $this->groupConfig['link_to'];
        }

        if (!isset($this->event['event_url'])) {
            return '';
        }

        return $this->event['event_url'];
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        $venue = isset($this->event['venue']) ? $this->event['venue'] : '';

        if ($venue) {
            return $venue['name'] . ', ' . $venue['address_1'] . ', ' . $venue['city'];
        }

        return '';
    }

    /**
     * @return string
     */
    public function getGroupName()
    {
        if(isset($this->event['group']) && isset($this->event['group']['name'])) {
            return $this->event['group']['name'];
        }

        return '';
    }

}
