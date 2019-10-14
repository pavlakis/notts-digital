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


$groups = $container['groups'];

$request = Zend\Diactoros\ServerRequestFactory::fromGlobals();

// index.php?group=PHPMinds

/** @var GetEventDetails $getEventDetails */
$getEventDetails = $container[GetEventDetails::class];

$server = new Zend\Diactoros\Server(
    static function(){},
    $request,
    $getEventDetails->getEvent($request)
);

$server->listen();