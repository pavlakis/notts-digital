<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2017 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */
use NottsDigital\Event\EventInterface;

date_default_timezone_set('Europe/London');

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/app/dependencies.php';


$groupsContainer = $container['groups.container'];

$request = Zend\Diactoros\ServerRequestFactory::fromGlobals();

// index.php?group=PHPMinds
$params = $request->getQueryParams();
$group = '';

// return empty json by default
$response = new Zend\Diactoros\Response\JsonResponse([
    'group'     => '',
    'subject'   => '',
    'description'   => '',
    'date_time' => '',
    'location'  => '',
    'event_url' => '',
    'iso_date' => '',
    'next_event' => []
]);

    if (!array_key_exists('group', $params)) {
        throw new \Exception('Group name required.', 400);
    }

    $group = $params['group'];
    if (!$groupsContainer->has($group)) {
        throw new \Exception('Group does not exist', 404);
    }

    $eventPlatform = $groupsContainer->get($group);

        $response = new Zend\Diactoros\Response\JsonResponse($event->toArray(), 200, [], JSON_PRETTY_PRINT);
    }

}

$response->withAddedHeader('Access-Control-Allow-Origin', '*');

$server = new Zend\Diactoros\Server(
    function(){},
    $request,
    $response
);

$server->listen();