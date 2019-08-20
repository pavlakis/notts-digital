<?php

namespace NottsDigital\tests\Http\Client;

use NottsDigital\Authentication\AuthenticationInterface,
    PHPUnit\Framework\MockObject\MockObject,
    DMS\Service\Meetup\MeetupOAuth2Client,
    PHPUnit\Framework\TestCase;
use NottsDigital\Http\Client\MeetupClient;

class MeetupClientTest extends TestCase
{
    /**
     * @var MeetupOAuth2Client|MockObject
     */
    private $httpClient;

    /**
     * @var AuthenticationInterface|MockObject
     */
    private $authentication;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(MeetupOAuth2Client::class);
        $this->authentication = $this->createMock(AuthenticationInterface::class);
    }

    /**
     * @test
     * @dataProvider meetupOAuth2ClientMethodsDataProvider
     * @param string $method
     */
    public function can_call_meetup_oath2_client_methods_when_not_authorised(string $method): void
    {
        $this->authentication->method('isAuthorised')->willReturn(false);
        $this->authentication->method('authorise');
        $this->httpClient->method('__call')->willReturn([]);

        $meetupClient = new MeetupClient($this->httpClient, $this->authentication);

        static::assertSame([], $meetupClient->$method());
    }

    public function meetupOAuth2ClientMethodsDataProvider(): array
    {
        return [
            ['getEvents'],
            ['getGroup']
        ];
    }

    /**
     * @test
     * @dataProvider meetupOAuth2ClientMethodsDataProvider
     * @param string $method
     */
    public function can_call_meetup_oath2_client_methods_when_not_authenticated(string $method): void
    {
        $this->authentication->method('isAuthorised')->willReturn(true);
        $this->authentication->method('isAuthenticated')->willReturn(false);
        $this->authentication->method('refresh');
        $this->httpClient->method('__call')->willReturn([]);

        $meetupClient = new MeetupClient($this->httpClient, $this->authentication);

        static::assertSame([], $meetupClient->$method());
    }
}
