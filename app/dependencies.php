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
    return json_decode(file_get_contents(__DIR__.'/configs/config.json'), true);
};

$container['groups'] = function($c){
    return json_decode(file_get_contents(__DIR__.'/configs/groups.json'), true);
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

    return \DMS\Service\Meetup\MeetupKeyAuthClient::factory(
        [
            'key' => $c['config']['meetups']['api-key'],
            'base_url' => $c['config']['meetups']['baseUrl']
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
        new \Doctrine\Common\Cache\FilesystemCache(realpath($cacheConfig['path'])), $cacheConfig['expiry']
    );
};

$container['meetup.request'] = function($c) {

    return new \NottsDigital\Http\Request\MeetupRequest(
        $c['meetupapi.client'],
        $c['file.cache'],
        $c['config']['meetups']['uris'],
        $c['groups']['meetups'],
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
