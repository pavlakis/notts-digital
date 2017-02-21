<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2017 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */
namespace NottsDigital\Tests\Adapter;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use NottsDigital\Adapter\MeetupAdapter;
use GuzzleHttp\Client;
use NottsDigital\Event\EventEntityCollection;
use NottsDigital\Http\Request\MeetupRequest;
use NottsDigital\Cache\Cache;

class MeetupAdapterTest extends \PHPUnit_Framework_TestCase
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
    private $singleEvent;

    /**
     * @var string json
     */
    private $multipleEvents;

    /**
     * @var \NottsDigital\Adapter\MeetupAdapter
     */
    protected $meetupAdapter;

    public function setUp()
    {
        $this->meetupRequestMock = $this->getMockBuilder(MeetupRequest::class);
        $this->config = require __DIR__ . '/feeders/config.php';
        $this->baseUrl = $this->config['meetups']['baseUrl'];

        $this->singleEvent = file_get_contents(__DIR__ . '/feeders/oneEventResponse.json');
        $this->multipleEvents = file_get_contents(__DIR__ . '/feeders/multipleEventResponse.json');

        $this->meetupAdapter = new MeetupAdapter(
            $this->config['meetups'],
            $this->meetupRequestMock->disableOriginalConstructor()->getMock(),
            new EventEntityCollection()
        );
    }
    
    public function testFetchValidGroupLoadsEvents()
    {
        $meetupRequestMock = $this->meetupRequestMock->disableOriginalConstructor()->getMock();
        $meetupRequestMock->method('fetchEventInfo')->willReturn(['results' => [ 0 => ['name' => 'Event Name']]]);

        $meetupAdapter = new MeetupAdapter(
            $this->config['meetups'],
            $meetupRequestMock,
            new EventEntityCollection()
        );


        $meetupAdapter->fetch('PHPMinds');

        $this->assertTrue($meetupAdapter->getEventEntityCollection()[0]->getTitle() === 'Event Name');
    }

    public function testFetchCanHandleMultipleEvents()
    {

        $meetupRequestMock = $this->meetupRequestMock->disableOriginalConstructor()->getMock();
        $meetupRequestMock->method('fetchEventInfo')
            ->willReturnCallback(function(){
            return \json_decode($this->multipleEvents, true);
        });

        $meetupAdapter = new MeetupAdapter(
            $this->config['meetups'],
            $meetupRequestMock,
            new EventEntityCollection()
        );

        $meetupAdapter->fetch('PHPMinds');

        $this->assertTrue($meetupAdapter->getEventEntityCollection()[0]->getTitle() === 'Industrial Control Cyber Security');
        $this->assertTrue($meetupAdapter->getEventEntityCollection()[1]->getTitle() === 'Current Postgraudate Research');
    }


    public function testFetchWillMatchNameCorrectly()
    {
    
        $jsonResponse = file_get_contents(__DIR__ . '/feeders/nameMatch.json');
        $meetupRequestMock = $this->meetupRequestMock->getMock();
        $meetupRequestMock->method('fetchEventInfo')
            ->willReturnCallback(function() use ($jsonResponse) {
                return \json_decode($jsonResponse, true);
            });

        $mockedCache = $this->getMockBuilder(Cache::class);
        $mockedCache = $mockedCache->disableOriginalConstructor()->getMock();
        $mockedCache->method('contains')->willReturn(true);
        $mockedCache->method('fetch')
             ->willReturn(file_get_contents(__DIR__ . '/feeders/nameMatch.json'));

        $meetupAdapter = new MeetupAdapter(
            $this->config['meetups'],
            $meetupRequestMock,
            new EventEntityCollection()
        );
        
        $meetupAdapter->fetch('Tech on Toast');

        $this->assertNotNull($meetupAdapter->getEventEntityCollection());
        $this->assertEquals($meetupAdapter->getEventEntityCollection()[0]->getTitle(), 'Tech on Toast January 2017 - The Launch!');
    }
    
    
    
    public function testFetchWillStillGetEventAsPartOfMainGroup()
    {
        
        $jsonResponse = file_get_contents(__DIR__ . '/feeders/nameMatch.json');
        $meetupRequestMock = $this->meetupRequestMock->getMock();
        $meetupRequestMock->method('fetchEventInfo')
            ->willReturnCallback(function() use ($jsonResponse) {
                return \json_decode($jsonResponse, true);
            });

        $mockedCache = $this->getMockBuilder(Cache::class);
        $mockedCache = $mockedCache->disableOriginalConstructor()->getMock();
        $mockedCache->method('fetch')
             ->willReturn(file_get_contents(__DIR__ . '/feeders/nameMatch.json'));
        
        $meetupAdapter = new MeetupAdapter(
            $this->config['meetups'],
            $meetupRequestMock,
            new EventEntityCollection()
        );
        
        $meetupAdapter->fetch('Tech Nottingham');
        
        $this->assertNotNull($meetupAdapter->getEventEntityCollection());
        $this->assertEquals(count($meetupAdapter->getEventEntityCollection()), 1);
    }
}