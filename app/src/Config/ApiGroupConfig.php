<?php

declare(strict_types=1);

namespace NottsDigital\Config;

use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use NottsDigital\Cache\Cache;

final class ApiGroupConfig implements GroupConfigInterface
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Cache
     */
    private $cache;

    public function __construct(
        string $url,
        Client $client,
        LoggerInterface $logger,
        Cache $cache
    ) {
        $this->url = $url;
        $this->client = $client;
        $this->logger = $logger;
        $this->cache = $cache;
    }

    /**
     * @return array
     *
     * @throw \RuntimeException
     * @throw \GuzzleHttp\Exception\RequestException
     */
    public function fetchConfig(): array
    {
        $cacheId = 'api_group_config';
        if (!$this->cache->contains($cacheId)) {
            try {
                $groupsJson = $this->client->get($this->url)->getBody()->getContents();

                if (!$this->isValid($groupsJson)) {
                    throw new \RuntimeException(
                        \sprintf('Groups %s is not a valid format', $groupsJson)
                    );
                }

                $this->cache->save($cacheId, $groupsJson);

                return $this->getGroups($groupsJson);

            } catch (\GuzzleHttp\Exception\RequestException $e) {
                $this->logger->error(
                    'Could not fetch group details with exception {exception}', [
                        'exception' => $e->getMessage(),
                ]);
                throw $e;
            } catch (\RuntimeException $e) {
                $this->logger->error(
                    'Could not fetch group details with exception {exception}', [
                    'exception' => $e->getMessage(),
                ]);
                throw $e;
            }
        }

        return $this->getGroups($this->cache->fetch($cacheId));
    }

    private function getGroups(string $groupsJson): array
    {
        $groups = \json_decode($groupsJson, true);

        if (!is_array($groups)) {
            throw new \RuntimeException('Could not retrieve groups');
        }

        return $groups;
    }

    private function isValid($groupsJson): bool
    {
        return is_string($groupsJson) && false !== \json_decode($groupsJson, true);
    }
}
