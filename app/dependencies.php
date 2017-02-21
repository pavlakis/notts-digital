<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2017 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */

use NottsDigital\Event\EventEntityCollection;
use NottsDigital\Http\Request\MeetupRequest;
use DMS\Service\Meetup\MeetupKeyAuthClient;
use Doctrine\Common\Cache\FilesystemCache;
use NottsDigital\Adapter\MeetupAdapter;
use NottsDigital\Group\GroupContainer;
use NottsDigital\Adapter\TitoAdapter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$container = new Pimple\Container();

$container['config'] = function($c){
    return json_decode(file_get_contents(__DIR__.'/configs/config.json'), true);
};

$container['groups'] = function($c){
    return json_decode(file_get_contents(__DIR__.'/configs/groups.json'), true);
};

$container['groups.container'] = function($c) {
  return new GroupContainer($c['groups']);
};

$container['api.log'] = function ($c) {
    $log = new Logger('api.log');
    $log->pushHandler(
        new StreamHandler(dirname(__DIR__). '/var/log/api.log',
            Logger::WARNING
        )
    );

    return $log;
};


$container['meetupapi.client'] = function ($c) {

    return MeetupKeyAuthClient::factory(
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
        new FilesystemCache(realpath($cacheConfig['path'])), $cacheConfig['expiry']
    );
};

$container['meetup.request'] = function($c) {

    return new MeetupRequest(
        $c['meetupapi.client'],
        $c['file.cache'],
        $c['config']['meetups']['uris'],
        $c['groups']['meetups'],
        $c['api.log']
    );

};

$container['adapter.meetups'] = function($c){
    return new MeetupAdapter(
        $c['groups']['meetups'],
        $c['meetup.request'],
        new EventEntityCollection()
    );
};

$container['event.meetups'] = function($c) {

    return new NottsDigital\Event\Event(
        $c['adapter.meetups']
    );
};

$container['adapter.tito'] = function($c){
    return new TitoAdapter(
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
