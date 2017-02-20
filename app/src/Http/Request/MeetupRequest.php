<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2017 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */

namespace NottsDigital\Http\Request;


use DMS\Service\Meetup\MeetupKeyAuthClient,
    Psr\Log\LoggerInterface,
    NottsDigital\Cache\Cache;
use Guzzle\Http\Exception\ClientErrorResponseException;

/**
 * Class MeetupRequest
 * @package NottsDigital\Http\Request
 */
class MeetupRequest
{
    /**
     * @var MeetupKeyAuthClient
     */
    private $httpClient;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $uris;

    /**
     * @var LoggerInterface
     */
    private $log;

    /**
     * MeetupRequest constructor.
     * @param MeetupKeyAuthClient $httpClient
     * @param Cache $cache
     * @param $uris
     * @param $config
     */
    public function __construct(
        MeetupKeyAuthClient $httpClient,
        Cache $cache,
        $uris,
        $config,
        LoggerInterface $log
    )
    {
        
        $this->log = $log;
        $this->uris = $uris;
        $this->cache = $cache;
        $this->config = $config;
        $this->httpClient = $httpClient;
    }

    /**
     * @param $groupUrlName
     * @param array $args
     * @return array
     * @throws \Exception
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
            } catch (ClientErrorResponseException $e) {
                $response = $e->getResponse();
                $this->log->alert($e->getMessage());
                throw new \Exception("Could not get event information.", $response->getStatusCode());

            } catch (\Exception $e) {
                $this->log->critical($e->getMessage());
                throw new \Exception("Could not get event information.", 400);
            }

        }

        $jsonResponse = $this->cache->fetch($cacheId);
        $events = \json_decode($jsonResponse, true);

        return $events;
    }

    /**
     * @param $groupUrlName
     * @return array
     * @throws \Exception
     */
    public function fetchGroupInfo($groupUrlName)
    {
        // if cached, return cache
        $cacheId = $groupUrlName . '_group-info';
        if (!$this->cache->contains($cacheId)) {

            try {

                $response =  $this->httpClient->getGroup(array('urlname' => $groupUrlName))->json();
                $this->cache->save($cacheId, \json_encode($response));
            } catch (ClientErrorResponseException $e) {
                $response = $e->getResponse();
                $this->log->alert($e->getMessage());
                throw new \Exception("Could not get group information.", $response->getStatusCode());

            } catch (\Exception $e) {
                $this->log->critical($e->getMessage());
                throw new \Exception("Could not get group information.", 400);
            }
        }

        $jsonResponse = $this->cache->fetch($cacheId);
        $groupInfo = \json_decode($jsonResponse, true);

        return $groupInfo;
    }
}