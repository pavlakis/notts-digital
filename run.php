<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2017 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */
date_default_timezone_set('Europe/London');

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/app/dependencies.php';

$groups = $container['groups'];
$request = $container['http.request'];

// index.php?group=PHPMinds
$params = $request->getQueryParams();

$getEventDetails = new \NottsDigital\Event\GetEventDetails($groups, $container['api.log'], $container['event.factory']);
$response = $getEventDetails->getEvent($params);

$response->withAddedHeader('Access-Control-Allow-Origin', '*');

$server = new Zend\Diactoros\Server(
    function(){},
    $request,
    $response
);

$server->listen();