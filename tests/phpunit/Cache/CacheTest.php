<?php
/**
 * Nottingham Digital events.
 *
 * @see      https://github.com/pavlakis/notts-digital
 *
 * @copyright Copyright (c) 2017 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */

namespace NottsDigital\Tests\Cache;

use NottsDigital\Cache\Cache;
use PHPUnit\Framework\TestCase;
use Doctrine\Common\Cache\FilesystemCache;

/**
 * Class CacheTest.
 */
class CacheTest extends TestCase
{
    /**
     * @var Cache
     */
    private $cache;

    protected function setUp(): void
    {
        $this->cache = new Cache(new FilesystemCache(dirname(dirname(dirname(__DIR__))).'/var/cache/'), 5);
    }

    public static function tearDownAfterClass(): void
    {
        $cache = new Cache(new FilesystemCache(dirname(dirname(dirname(__DIR__))).'/var/cache/'), 5);
        $cache->flushAll();
    }

    public function test_can_cache_string()
    {
        $this->cache->save('test1', 'abc123...');
        static::assertTrue($this->cache->contains('test1'));
        static::assertEquals('abc123...', $this->cache->fetch('test1'));
    }

    public function test_expired_cache_gets_overriden()
    {
        $cache = new Cache(new FilesystemCache(dirname(dirname(dirname(__DIR__))).'/var/cache/'), 1);
        $cache->save('test1', 'abc123...');
        sleep(1);
        static::assertNotTrue($cache->contains('test1'));
        $cache->save('test1', '123abc');
        static::assertEquals('123abc', $cache->fetch('test1'));
    }
}
