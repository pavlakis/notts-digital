<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2017 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */

namespace NottsDigital\Http\Request;

use NottsDigital\Http\Client\MeetupClient,
    Psr\Log\LoggerInterface,
    NottsDigital\Cache\Cache;

/**
 * Class MeetupRequest
 * @package NottsDigital\Http\Request
 */
class MeetupRequest
{
    /**
     * @var MeetupClient
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
     * @param MeetupClient      $httpClient
     * @param Cache             $cache
     * @param LoggerInterface   $log
     */
    public function __construct(
        MeetupClient $httpClient,
        Cache $cache,
        LoggerInterface $log
    ) {
        $this->log = $log;
        $this->cache = $cache;
        $this->httpClient = $httpClient;
    }

    /**
     * @param string $groupUrlName
     * @param array $args
     * @return array
     */
    public function fetchEventInfo($groupUrlName, $args = ['status' => 'upcoming']): array
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

        return \json_decode($jsonResponse, true);
    }

    /**
     * @param string $groupUrlName
     * @return array
     */
    public function fetchGroupInfo(string $groupUrlName): array
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

        return \json_decode($jsonResponse, true);
    }
}