<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2016 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */
date_default_timezone_set('Europe/London');

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/app/dependencies.php';


$config = $container['config'];

$request = Zend\Diactoros\ServerRequestFactory::fromGlobals();

// index.php?group=PHPMinds
$params = $request->getQueryParams();
$group = '';

// return empty json by default
$response = new Zend\Diactoros\Response\JsonResponse([
    'group'     => '',
    'subject'   => '',
    'date_time' => '',
    'location'  => '',
    'event_url' => ''
]);

if (array_key_exists('group', $params)) {

    $group = $params['group'];
    $eventType = '';

    foreach ($config as $type => $events) {
        if (array_key_exists($group, $events)) {
            $eventType = $type;
            break;
        }
    }

    if ($container->offsetExists('event.' . $eventType) === true) {
        $event = $container['event.' . $eventType];
        $event->getByGroup($group);

        $response = new Zend\Diactoros\Response\JsonResponse($event->toArray());
    }

}

$response->withAddedHeader('Access-Control-Allow-Origin', '*');

$server = new Zend\Diactoros\Server(
    function(){},
    $request,
    $response
);

$server->listen();