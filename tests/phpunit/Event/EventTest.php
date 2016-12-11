<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2016 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */
namespace NottsDigital\Tests\Event;

use NottsDigital\Adapter\MeetupAdapter;
use NottsDigital\Event\Event;
use NottsDigital\Event\EventEntityCollection;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
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
     * @var string json
     */
    private $singleEventResponse;

    /**
     * @var string json
     */
    private $multipleEventResponse;

    /**
     * @var string json
     */
    private $groupReponse;

    /**
     * @var \NottsDigital\Adapter\MeetupAdapter
     */
    protected $meetupAdapter;

    protected $event;

    public function setUp()
    {
        $this->httpClient = $this->getMockBuilder('GuzzleHttp\Client')->setMethods(['get']);
        $this->config = require dirname(__DIR__) . '/Adapter/feeders/config.php';
        $this->baseUrl = $this->config['meetups']['baseUrl'];

        $this->singleEventResponse = file_get_contents(dirname(__DIR__) . '/Adapter/feeders/oneEventResponse.json');
        $this->multipleEventResponse = file_get_contents(dirname(__DIR__) . '/Adapter/feeders/multipleEventResponse.json');
        $this->groupReponse = file_get_contents(dirname(__DIR__) . '/Adapter/feeders/groupsBCSResponse.json');

        $this->meetupAdapter = new MeetupAdapter(
            $this->httpClient->getMock(), $this->apiKey, $this->baseUrl, $this->config['meetups']['uris'], $this->config['meetups'], new EventEntityCollection()
        );

    }

    public function testEventCanHandleMultipleEvents()
    {
        $response = $this->getMockBuilder('Psr\Http\Message\ResponseInterface')->getMockForAbstractClass();
        $msgBody = $this->getMockBuilder('Psr\Http\Message\StreamInterface')->setMethods(['getContents'])->getMockForAbstractClass();
        $msgBody->expects(static::at(0))->method('getContents')
            ->willReturnCallback(function () {
                return $this->multipleEventResponse;
            });

        $msgBody->expects(static::at(1))->method('getContents')
            ->willReturnCallback(function () {
                return $this->groupReponse;
            });


        $httpClient = $this->httpClient->getMock();
        $httpClient->method('get')
            ->willReturn($response);

        $response->method('getBody')
            ->willReturnCallback(function () use ($msgBody) {
                return $msgBody;
            });


        $meetupAdapter = new MeetupAdapter(
            $httpClient, $this->apiKey, $this->baseUrl, $this->config['meetups']['uris'], $this->config['meetups'], new EventEntityCollection()
        );


        $event = new Event($meetupAdapter);
        $event->getByGroup('BCS-Leicester');

        $responseArray = $event->toArray();
        
        static::assertTrue(!empty($responseArray['next_event']));
        static::assertArrayHasKey('description', $responseArray);
        static::assertTrue($responseArray['subject'] === 'Industrial Control Cyber Security');
        static::assertTrue($responseArray['next_event']['subject'] === 'Current Postgraudate Research');
        static::assertTrue($responseArray['group'] === 'BCS Leicester');
        static::assertTrue($responseArray['group_photo'] === 'http://photos1.meetupstatic.com/photos/event/6/b/6/3/highres_431727491.jpeg');
    }
}