<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2016 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */

namespace NottsDigital\Http;


use GuzzleHttp\Client,
    Doctrine\Common\Cache\FilesystemCache;

final class Request
{
    public function __construct(Client $httpClient, FilesystemCache $cache)
    {

    }

    protected function fetchEventInfo($groupUrlName)
    {
        // if cached, return cache
        if ($this->isCached($groupUrlName . '_event-info')) {

        }

        $response = $this->client->get($this->baseUrl . sprintf($this->uris['events'], $groupUrlName, $this->apiKey));
        $events = json_decode($response->getBody()->getContents(), true);

        return $events;
    }

    protected function fetchGroupInfo($groupUrlName)
    {
        // if cached, return cache
        if ($this->isCached($groupUrlName . '_group-info')) {

        }

        $response = $this->client->get($this->baseUrl . sprintf($this->uris['groups'], $groupUrlName, $this->apiKey));

        $groupInfo = json_decode($response->getBody()->getContents(), true);

        return $groupInfo;
    }

    protected function isCached($group)
    {
        // check if available and not expired
        return $this->cache->contains($group);
    }

    protected function getFromCache($group)
    {
        return $this->cache->fetch($group);
    }

    protected function cache($group, $expires)
    {

    }

}