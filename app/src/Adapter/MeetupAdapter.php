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
    protected $uris;

    /**
     * @var array
     */
    protected $event = [];

    /**
     * @var array
     */
    protected $groupConfig = [];

    /**
     * MeetupAdapter constructor.
     * @param Client $client
     * @param $apiKey
     * @param $baseUrl
     * @param $uris
     * @param $config
     */
    public function __construct(Client $client, $apiKey, $baseUrl, $uris, $config)
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
        $this->baseUrl = $baseUrl;
        $this->uris = $uris;
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
        
        $this->loadEventInfo($group);

        $this->loadGroupInfo($group);

    }

    /**
     * @param $group
     */
    protected function loadEventInfo($group)
    {
        $groupUrlName = $this->config[$group]['group_urlname'];
        $response = $this->client->get($this->baseUrl .  sprintf($this->uris['events'], $groupUrlName, $this->apiKey));
        $events = json_decode($response->getBody()->getContents(), true);

        if (isset($events['results']) && !empty($events['results'])) {
            if (isset($this->config[$group]['match']) && isset($this->config[$group]['match']['name'])) {
                $this->event = $this->getByNameStringMatch($events['results'], $this->config[$group]['match']['name']);
            } else {

                $this->event = $events['results'][0];
            }

            $this->groupConfig = $this->config[$group];
        }
    }

    /**
     * @param $group
     */
    protected function loadGroupInfo($group)
    {
        $groupUrlName = $this->config[$group]['group_urlname'];
        $response = $this->client->get($this->baseUrl .  sprintf($this->uris['groups'], $groupUrlName, $this->apiKey));
        $groupInfo = json_decode($response->getBody()->getContents(), true);

        if (isset($groupInfo['results']) && !empty($groupInfo['results'])) {
            $groupInfo = $groupInfo['results'][0];
            $this->event['group_info'] = [];
            $this->event['group_info']['description'] = $groupInfo['description'];
            $this->event['group_info']['group_photo'] = $groupInfo['group_photo'];
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

    /**
     * @return array
     */
    public function getGroupInfo()
    {
        if (isset($this->event['group_info'])) {
            return $this->event['group_info'];
        }

        return [];
    }
}
