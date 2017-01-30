<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2017 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */
namespace NottsDigital\Cache;

use Doctrine\Common\Cache\FilesystemCache;

/**
 * Class Cache
 * @package NottsDigital\Cache
 */
class Cache
{
    /**
     * @var FilesystemCache
     */
    private $cache;

    /**
     * @var
     */
    private $lifetime;

    /**
     * Cache constructor.
     * @param FilesystemCache $cache
     * @param $lifetime
     */
    public function __construct(FilesystemCache $cache, $lifetime)
    {
        $this->cache = $cache;
        $this->lifetime = $lifetime;
    }

    /**
     * @param $id
     * @return bool
     */
    public function contains($id)
    {
        return $this->cache->contains($id);
    }

    /**
     * @param $id
     * @return false|mixed
     */
    public function fetch($id)
    {
        return $this->cache->fetch($id);
    }

    /**
     * @param $id
     * @param $data
     * @return bool
     */
    public function save($id, $data)
    {
        return $this->cache->save($id, $data, $this->lifetime);
    }

    /**
     * @return bool
     */
    public function flushAll()
    {
        return $this->cache->flushAll();
    }
}