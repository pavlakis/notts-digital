<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2016 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */

$container = new Pimple\Container();

$container['config'] = function($c){
    return json_decode(file_get_contents(__DIR__.'/configs/config.json'), true);
};

$container['http.client'] = function($c) {
    return new GuzzleHttp\Client();
};

$container['http.crawler'] = function($c) {
    return new Goutte\Client();
};

$container['http.request'] = function($c){
    return Zend\Diactoros\ServerRequestFactory::fromGlobals();
};

$container['adapter.meetups'] = function($c){
    return new \NottsDigital\Adapter\MeetupAdapter(
        $c['http.client'],
        $c['config']['meetups']['api-key'],
        $c['config']['meetups']
    );
};

$container['event.meetups'] = function($c) {

    return new NottsDigital\Event\MeetupEvent(
        $c['adapter.meetups']
    );
};

$container['adapter.tito'] = function($c){
    return new \NottsDigital\Adapter\TitoAdapter(
        $c['http.crawler'],
        $c['config']['ti.to']
    );
};

$container['event.ti.to'] = function($c) {

    return new NottsDigital\Event\TitoEvent(
        $c['adapter.tito']
    );
};