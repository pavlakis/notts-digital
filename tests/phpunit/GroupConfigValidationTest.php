<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2017 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */
namespace NottsDigital\Tests;

use PHPUnit\Framework\TestCase;

class GroupConfigValidationTest extends TestCase
{
    /**
     * @var array
     */
    private $groupConfig;

    protected function setUp(): void
    {
        $rootDir = dirname(dirname(__DIR__));
        $this->groupConfig = include($rootDir . '/app/configs/groups.php');
    }

    /**
     * @test
     * @dataProvider serviceDataProvider
     * @param $service
     */
    public function group_config_includes_supported_service($service)
    {
        static::assertArrayHasKey($service, $this->groupConfig);
    }

    /**
     * @test
     */
    public function group_config_supports_two_services()
    {
        static::assertCount(2, $this->groupConfig);
    }

    /**
     * @test
     */
    public function meetup_groups_include_group_url_name()
    {
        foreach ($this->groupConfig['meetups'] as $meetup) {
            static::assertArrayHasKey('group_urlname', $meetup);
        }
    }

    public function serviceDataProvider()
    {
        return [
            ['meetups'],
            ['ti.to']
        ];
    }
}