<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2017 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */
namespace NottsDigital\Tests;

class GroupConfigValidationTest extends \PHPUnit_Framework_TestCase
{

    public function testGroupConfigIsValidJson()
    {
        $rootDir = dirname(dirname(__DIR__));
        
        $groupConfig = \json_decode(file_get_contents($rootDir . '/app/configs/groups.json'), true);

        static::assertTrue(is_array($groupConfig));
    }
}