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

$container['token.filename'] = function($c){
    return dirname(__DIR__) . '/.token';
};

$container['token.provider'] = function($c){
    return new \NottsDigital\Authentication\TokenProvider($c['token.filename']);
};

$container['meetup.authentication'] = function($c){
    return new \NottsDigital\Authentication\OAuth2($c['oauth2.provider'], $c['token.provider'], $c['http.request']);
};

$container['meetup.client'] = function($c){
    return new \NottsDigital\Http\Client\MeetupClient(
        $c['meetup.httpclient'],
        $c['meetup.authentication']
    );
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

$container['oauth2.provider'] = function ($c) {
    $provider = new \WitteStier\OAuth2\Client\Provider\Meetup([
        'clientId' => $c['config']['meetups']['consumer_key'],
        'clientSecret' => $c['config']['meetups']['consumer_secret'],
        'redirectUri' => $c['config']['meetups']['redirect_uri'],
    ]);

    return $provider;
};

$container['meetup.httpclient'] = function ($c) {

    return \DMS\Service\Meetup\MeetupOAuth2Client::factory([
        'access_token' => $c['token.provider']->getToken()
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
        $c['meetup.client'],
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