<?php

namespace NottsDigital\Http\Client;

use NottsDigital\Authentication\AuthenticationInterface,
    DMS\Service\Meetup\Response\MultiResultResponse,
    DMS\Service\Meetup\MeetupOAuth2Client;

/**
 * @method MultiResultResponse getEvents(array $args = array())
 * @method MultiResultResponse getGroup(array $args = array())
 */
class MeetupClient
{
    /**
     * @var MeetupOAuth2Client
     */
    private $httpClient;

    /**
     * @var AuthenticationInterface
     */
    private $authentication;

    public function __construct(MeetupOAuth2Client $httpClient, AuthenticationInterface $authentication)
    {
        $this->httpClient = $httpClient;
        $this->authentication = $authentication;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $this->verifyAuthentication();
        return $this->httpClient->$name(...$arguments);
    }

    /**
     * @return void
     */
    private function verifyAuthentication(): void
    {
        if (!$this->authentication->isAuthorised()) {
            $this->authentication->authorise();
            return;
        }

        if (!$this->authentication->isAuthenticated()) {
            $this->authentication->refresh();
        }
    }
}
