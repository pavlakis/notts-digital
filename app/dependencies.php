<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2017 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */

$container = new Pimple\Container();

$container['config'] = function($c){
    return include __DIR__.'/configs/config.php';
};

$container['groups'] = function($c){
    return include __DIR__.'/configs/groups.php';
};

$container['api.log'] = function ($c) {
    $log = new \Monolog\Logger('api.log');
    $log->pushHandler(
        new \Monolog\Handler\StreamHandler(dirname(__DIR__). '/var/log/api.log',
            \Monolog\Logger::WARNING
        )
    );

    return $log;
};


$container['meetupapi.client'] = function ($c) {

    return \DMS\Service\Meetup\MeetupOAuthClient::factory([
        'consumer_key'    => $c['config']['meetups']['consumer_key'],
        'consumer_secret'    => $c['config']['meetups']['consumer_secret'],
    ]);
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

$container['file.cache'] = function($c) {
    $cacheConfig = $c['config']['cache'];
    return new NottsDigital\Cache\Cache(
        new \Doctrine\Common\Cache\FilesystemCache((string) realpath($cacheConfig['path'])),
        (int) $cacheConfig['expiry']
    );
};

$container['meetup.request'] = function($c) {

    return new \NottsDigital\Http\Request\MeetupRequest(
        $c['meetupapi.client'],
        $c['file.cache'],
        $c['api.log']
    );

};

$container['adapter.meetups'] = function($c){
    return new \NottsDigital\Adapter\MeetupAdapter(
        $c['groups']['meetups'],
        $c['meetup.request'],
        new \NottsDigital\Event\EventEntityCollection()
    );
};

$container['event.meetups'] = function($c) {

    return new NottsDigital\Event\Event(
        $c['adapter.meetups']
    );
};

$container['adapter.tito'] = function($c){
    return new \NottsDigital\Adapter\TitoAdapter(
        $c['http.crawler'],
        $c['config']['ti.to']['baseUrl'],
        $c['groups']['ti.to']
    );
};

$container['event.ti.to'] = function($c) {

    return new NottsDigital\Event\Event(
        $c['adapter.tito']
    );
};

$container['event.factory'] = function($c) {
    return new NottsDigital\Event\EventFactory(
        $c['adapter.meetups'],
        $c['adapter.tito']
    );
};

$container[\NottsDigital\Event\GetEventDetails::class] = function($c) {
    return new \NottsDigital\Event\GetEventDetails(
        $c['groups'],
        $c['event.factory']
    );
};