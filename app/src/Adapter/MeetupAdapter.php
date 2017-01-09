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
use NottsDigital\Event\EventEntity;
use NottsDigital\Event\EventEntityCollection;
use NottsDigital\Event\GroupInfo;
use NottsDigital\Event\NullEventEntity;
use NottsDigital\Event\NullGroupInfo;

/**
 * Class MeetupAdapter
 * @package NottsDigital\Adapter
 */
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
    protected $groupConfig = [];

    /**
     * @var EventEntityCollection
     */
    protected $eventEntityCollection;

    /**
     * @var GroupInfo
     */
    protected $groupInfo;

    /**
     * MeetupAdapter constructor.
     * @param Client $client
     * @param $apiKey
     * @param $baseUrl
     * @param $uris
     * @param $config
     * @param EventEntityCollection $eventEntityCollection
     */
    public function __construct(Client $client, $apiKey, $baseUrl, $uris, $config, EventEntityCollection $eventEntityCollection)
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
        $this->baseUrl = $baseUrl;
        $this->uris = $uris;
        $this->config = $config;
        $this->eventEntityCollection = $eventEntityCollection;
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

        try {

            $response = $this->client->get($this->baseUrl . sprintf($this->uris['events'], $groupUrlName, $this->apiKey));
            $events = json_decode($response->getBody()->getContents(), true);

            if (!isset($events['results']) || empty($events['results'])) {
                throw new \Exception('No events found.');
            }

            $this->groupConfig = $this->config[$group];

            if (isset($this->config[$group]['match']) && isset($this->config[$group]['match']['name'])) {
                $this->eventEntityCollection->add(
                    new EventEntity($this->getByNameStringMatch($events['results'], $this->config[$group]['match']['name']))
                );
            } else {
                
                $this->eventEntityCollection->add(new EventEntity($events['results'][0], $this->groupConfig));

                if (isset($events['results'][1])) {
                    $this->eventEntityCollection->add(new EventEntity($events['results'][1], $this->groupConfig));
                }
            }

        } catch (\Exception $e) {
            $this->eventEntityCollection->add(new NullEventEntity());
        }

    }

    /**
     * @param $group
     */
    protected function loadGroupInfo($group)
    {
        $groupUrlName = $this->config[$group]['group_urlname'];

        try {

            $response = $this->client->get($this->baseUrl . sprintf($this->uris['groups'], $groupUrlName, $this->apiKey));

            $groupInfo = json_decode($response->getBody()->getContents(), true);

            if (isset($groupInfo['results']) && !empty($groupInfo['results'])) {
                $groupInfo = $groupInfo['results'][0];

                $this->groupInfo = new GroupInfo($groupInfo['name'], $groupInfo['description'], $groupInfo['group_photo']['highres_link']);
            } else {
                $this->groupInfo = new NullGroupInfo();
            }

        } catch (\Exception $e) {
            $this->groupInfo = new NullGroupInfo();
        }
    }

    /**
     * @param $events
     * @param $nameMatch
     * @return bool
     */
    protected function getByNameStringMatch($events, $nameMatch)
    {
        $nameMatch = $this->normaliseName($nameMatch);
        foreach ($events as $event) {
            $eventName = $this->normaliseName($event['name']);
            if (strpos($eventName, $nameMatch) !== false) {
                return $event;
            }
        }
        return [];
    }

    /**
    *@param $name
    *@return string 
    *
    */
    protected function normaliseName($name)
    {
        $name = preg_replace('/\s*/', '',strtolower($name));

        return $name;
    }

    /**
     * @return string
     */
    public function getGroupName()
    {
        return $this->groupInfo->getGroupName();
    }

    /**
     * @return string
     */
    public function getGroupDescription()
    {
        return $this->groupInfo->getGroupDescription();
    }

    /**
     * @return string
     */
    public function getGroupPhoto()
    {
        return $this->groupInfo->getGroupPhoto();
    }

    /**
     * @return array
     */
    public function getEventEntityCollection()
    {
        return $this->eventEntityCollection;
    }
}
