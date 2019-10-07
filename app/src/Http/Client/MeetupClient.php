<?php

namespace NottsDigital\Http\Client;

use DMS\Service\Meetup\AbstractMeetupClient;
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
     * @var AbstractMeetupClient
     */
    private $httpClient;

    /**
     * @var AuthenticationInterface
     */
    private $authentication;

    public function __construct(AbstractMeetupClient $httpClient, AuthenticationInterface $authentication)
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
        if ($this->httpClient instanceof MeetupOAuth2Client) {
            return;
        }

        if (!$this->authentication->isAuthorised()) {
            $this->authentication->authorise();
            return;
        }

        if (!$this->authentication->isAuthenticated()) {
            $this->authentication->refresh();
        }
    }
}
