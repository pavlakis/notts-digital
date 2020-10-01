<?php

declare(strict_types=1);

namespace NottsDigital\Config;

use GuzzleHttp\Client;

final class ApiGroupConfig implements GroupConfigInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $url;

    public function __construct(Client $client, string $url)
    {
        $this->client = $client;
        $this->url = $url;
    }

    public function fetchConfig(): array
    {
        try {
            $groupsJson = $this->client->get($this->url)->getBody()->getContents();
        } catch (\Exception $e) {
            return [];
        }

        $groups = \json_decode($groupsJson, true);

        if (!is_array($groups)) {
            throw new \RuntimeException('Could not retrieve groups');
        }

        return $groups;
    }
}
