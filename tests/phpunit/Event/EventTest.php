<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2017 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */
namespace NottsDigital\Tests\Event;

use NottsDigital\Adapter\MeetupAdapter;
use NottsDigital\Event\Event;
use NottsDigital\Event\EventEntityCollection;
use NottsDigital\Http\Request\MeetupRequest;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    /**
     * @var MeetupRequest
     */
    protected $meetupRequestMock;

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

        $this->meetupRequestMock = $this->getMockBuilder(MeetupRequest::class)->setMethods([
            'fetchEventInfo',
            'fetchGroupInfo'
        ]);

        $this->meetupAdapter = new MeetupAdapter(
            $this->config['meetups'],
            $this->meetupRequestMock->disableOriginalConstructor()->getMock(),
            new EventEntityCollection()
        );

    }

    public function testEventCanHandleMultipleEvents()
    {
        $meetupRequestMock = $this->meetupRequestMock->disableOriginalConstructor()->getMock();
        $meetupRequestMock->method('fetchEventInfo')
            ->willReturnCallback(function(){
                return \json_decode($this->multipleEventResponse, true);
            });

        $meetupRequestMock->method('fetchGroupInfo')
            ->willReturnCallback(function(){
                $results = \json_decode($this->groupReponse, true);
                return $results['results'][0];
            });

        $meetupAdapter = new MeetupAdapter(
            $this->config['meetups'],
            $meetupRequestMock,
            new EventEntityCollection()
        );


        $event = new Event($meetupAdapter);
        $event->getByGroup('BCS-Leicester');

        $responseArray = $event->toArray();
        
        static::assertTrue(!empty($responseArray['next_event']));
        static::assertArrayHasKey('description', $responseArray);
        static::assertEquals('Industrial Control Cyber Security', $responseArray['subject']);
        static::assertEquals('Current Postgraudate Research', $responseArray['next_event']['subject']);
        static::assertEquals('BCS Leicester', $responseArray['group']);
        static::assertEquals('http://photos1.meetupstatic.com/photos/event/6/b/6/3/highres_431727491.jpeg', $responseArray['group_photo']);
    }
}