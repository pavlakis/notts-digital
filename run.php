<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2017 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */

use NottsDigital\Event\GetEventDetails;

date_default_timezone_set('Europe/London');

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/app/dependencies.php';

// index.php?group=PHPMinds

/** @var GetEventDetails $getEventDetails */
$getEventDetails = $container[GetEventDetails::class];
$request = $container['http.request'];

$server = new Laminas\Diactoros\Server(
    static function(){},
    $request,
    $getEventDetails->getEvent($container['http.request'])
);

$server->listen();