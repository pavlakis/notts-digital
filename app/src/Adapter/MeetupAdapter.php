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
    protected $baseUrl = 'https://api.meetup.com/2/events/?group_urlname=%s&key=%s';

    public function __construct(Client $client, $apiKey, $config)
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
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
                return $this->getByNameStringMatch($events['results'], $this->config[$group]['match']);
            }

            return $events['results'][0];
        }

        return [];
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
}
