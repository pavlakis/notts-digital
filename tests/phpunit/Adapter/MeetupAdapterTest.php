<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2016 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */
namespace NottsDigital\Tests\Adapter;

use NottsDigital\Adapter\MeetupAdapter;
use GuzzleHttp\Client;

class MeetupAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $apiKey = 'abc123';

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var
     */
    protected $config;

    /**
     * @var \NottsDigital\Adapter\MeetupAdapter
     */
    protected $meetupAdapter;

    public function setUp()
    {
        $this->httpClient = $this->getMock('GuzzleHttp\Client', ['get']);
        $this->config = require __DIR__ . '/feeders/config.php';
        $this->baseUrl = $this->config['meetups']['baseUrl'];

        $this->meetupAdapter = new MeetupAdapter(
            $this->httpClient, $this->apiKey, $this->baseUrl, $this->config['meetups']
        );
    }

    public function testFetchGroupNotExistsReturnsEmptyArray()
    {
        $events = $this->meetupAdapter->fetch('nothing');

        $this->assertEquals([], $events);
    }

    public function testFetchValidGroupLoadsEvents()
    {
        $response = $this->getMock('Psr\Http\Message\ResponseInterface');
        $msgBody = $this->getMock('Psr\Http\Message\StreamInterface');

        $this->httpClient->method('get')
            ->willReturn($response);

        $response->method('getBody')
            ->willReturn($msgBody);

        $msgBody->method('getContents')
            ->willReturn(json_encode(['results' => [ 0 => ['name' => 'Event Name']]]));


        $this->meetupAdapter->fetch('PHPMinds');

        $this->assertTrue($this->meetupAdapter->getTitle() === 'Event Name');
    }
}