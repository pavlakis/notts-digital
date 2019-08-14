<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2017 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */

namespace NottsDigital\Http\Request;


use DMS\Service\Meetup\MeetupOAuthClient,
    Psr\Log\LoggerInterface,
    NottsDigital\Cache\Cache;

/**
 * Class MeetupRequest
 * @package NottsDigital\Http\Request
 */
class MeetupRequest
{
    /**
     * @var MeetupOAuthClient
     */
    private $httpClient;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var LoggerInterface
     */
    private $log;

    /**
     * @param MeetupOAuthClient $httpClient
     * @param Cache             $cache
     * @param LoggerInterface   $log
     */
    public function __construct(
        MeetupOAuthClient $httpClient,
        Cache $cache,
        LoggerInterface $log
    )
    {

        $this->log = $log;
        $this->cache = $cache;
        $this->httpClient = $httpClient;
    }

    /**
     * @param string $groupUrlName
     * @param array $args
     * @return array
     */
    public function fetchEventInfo($groupUrlName, $args = ['status' => 'upcoming'])
    {
        // if cached, return cache
        $cacheId = $groupUrlName . '_event-info';

        if (!$this->cache->contains($cacheId)) {

            try {
                $eventArgs = array_merge(['group_urlname' => $groupUrlName], $args);
                $response = $this->httpClient->getEvents($eventArgs)->json();
                $this->cache->save($cacheId, \json_encode($response));
            } catch (\Exception $e) {
                $this->log->alert($e->getMessage());
            }

        }

        $jsonResponse = $this->cache->fetch($cacheId);
        $events = \json_decode($jsonResponse, true);

        return $events;
    }

    /**
     * @param $groupUrlName
     * @return array
     */
    public function fetchGroupInfo($groupUrlName)
    {
        // if cached, return cache
        $cacheId = $groupUrlName . '_group-info';
        if (!$this->cache->contains($cacheId)) {

            try {

                $response =  $this->httpClient->getGroup(array('urlname' => $groupUrlName))->json();
                $this->cache->save($cacheId, \json_encode($response));
            } catch (\Exception $e) {
                $this->log->alert($e->getMessage());
            }
        }

        $jsonResponse = $this->cache->fetch($cacheId);
        $groupInfo = \json_decode($jsonResponse, true);

        return $groupInfo;
    }
}