<?php

namespace NottsDigital\Authentication;

use League\OAuth2\Client\Grant\AuthorizationCode;
use League\OAuth2\Client\Token\AccessTokenInterface,
    WitteStier\OAuth2\Client\Provider\Meetup;

class OAuth2 implements AuthenticationInterface
{
    /**
     * @var Meetup
     */
    private $provider;

    /**
     * @var TokenProvider
     */
    private $tokenProvider;

    public function __construct(Meetup $provider, TokenProvider $tokenProvider)
    {
        $this->provider = $provider;
        $this->tokenProvider = $tokenProvider;
    }

    public function isAuthenticated(): bool
    {
        return !$this->getAccessToken()->hasExpired();
    }

    public function isAuthorised(): bool
    {
        return !empty($this->tokenProvider->getToken());
    }

    private function getAccessToken(): AccessTokenInterface
    {
        return $this->provider->getAccessToken('authorization_code', [
            'code' => $this->tokenProvider->getToken()
        ]);
    }

    public function authorise(): void
    {
        if (isset($_GET['code'])) {
            $this->tokenProvider->saveToken($_GET['code']);
            return;
        }

        $this->requestAuthorisation();
    }

    private function requestAuthorisation(): void
    {
        if (isset($_GET['authorise']) && 'meetup' === $_GET['authorise']) {
            header('Location: ' . $this->provider->getAuthorizationUrl());
            exit;
        }
    }

    public function refresh(): void
    {
        $newAccessToken = $this->provider->getAccessToken('refresh_token', [
            'refresh_token' => $this->getAccessToken()->getRefreshToken()
        ]);

        $this->tokenProvider->saveToken($newAccessToken->getToken());
    }
}
