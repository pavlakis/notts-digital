<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2017 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */

namespace NottsDigital\Http\Request;


use GuzzleHttp\Client,
    NottsDigital\Cache\Cache;

/**
 * Class MeetupRequest
 * @package NottsDigital\Http\Request
 */
class MeetupRequest
{
    /**
     * @var Client
     */
    private $httpClient;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var array
     */
    private $config;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var array
     */
    private $uris;

    /**
     * MeetupRequest constructor.
     * @param Client $httpClient
     * @param Cache $cache
     * @param $apiKey
     * @param $baseUrl
     * @param $uris
     * @param $config
     */
    public function __construct(
        Client $httpClient,
        Cache $cache,
        $apiKey,
        $baseUrl,
        $uris,
        $config
    )
    {
        $this->uris = $uris;
        $this->cache = $cache;
        $this->config = $config;
        $this->apiKey = $apiKey;
        $this->baseUrl = $baseUrl;
        $this->httpClient = $httpClient;
    }

    /**
     * @param $groupUrlName
     * @return array
     */
    public function fetchEventInfo($groupUrlName)
    {
        // if cached, return cache
        $cacheId = $groupUrlName . '_event-info';

        if (!$this->cache->contains($cacheId)) {

            try {

                $response = $this->httpClient->get($this->baseUrl . sprintf($this->uris['events'], $groupUrlName, $this->apiKey));
                $this->cache->save($cacheId, $response->getBody()->getContents());
            } catch (\Exception $e) {
                // todo - add logging
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

                $response = $this->httpClient->get($this->baseUrl . sprintf($this->uris['groups'], $groupUrlName, $this->apiKey));
                $this->cache->save($cacheId, $response->getBody()->getContents());
            } catch (\Exception $e) {
                // todo - add logging
            }
        }

        $jsonResponse = $this->cache->fetch($cacheId);

        $groupInfo = \json_decode($jsonResponse, true);

        return $groupInfo;
    }
}