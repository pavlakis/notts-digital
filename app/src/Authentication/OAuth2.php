<?php

namespace NottsDigital\Authentication;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException,
    League\OAuth2\Client\Token\AccessTokenInterface,
    WitteStier\OAuth2\Client\Provider\Meetup,
    Psr\Http\Message\ServerRequestInterface;

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

    /**
     * @var ServerRequestInterface
     */
    private $request;

    public function __construct(Meetup $provider, TokenProvider $tokenProvider, ServerRequestInterface $request)
    {
        $this->provider = $provider;
        $this->tokenProvider = $tokenProvider;
        $this->request = $request;
    }

    /**
     * @return bool
     * @throws IdentityProviderException
     */
    public function isAuthenticated(): bool
    {
        return !$this->getAccessToken()->hasExpired();
    }

    /**
     * @return bool
     */
    public function isAuthorised(): bool
    {
        return !empty($this->tokenProvider->getToken());
    }

    /**
     * @return AccessTokenInterface
     * @throws IdentityProviderException
     */
    private function getAccessToken(): AccessTokenInterface
    {
        return $this->provider->getAccessToken('authorization_code', [
            'code' => $this->tokenProvider->getToken()
        ]);
    }

    /**
     * @return void
     */
    public function authorise(): void
    {
        if (null !== $this->getRequestParam('code')) {
            $this->tokenProvider->saveToken($this->getRequestParam('code'));
            return;
        }

        $this->requestAuthorisation();
    }

    /**
     * @return void
     */
    private function requestAuthorisation(): void
    {
        if ('meetup' === $this->getRequestParam('authorise', '')) {
            header('Location: ' . $this->provider->getAuthorizationUrl());
            exit;
        }
    }

    /**
     * @throws IdentityProviderException
     */
    public function refresh(): void
    {
        $newAccessToken = $this->provider->getAccessToken('refresh_token', [
            'refresh_token' => $this->getAccessToken()->getRefreshToken()
        ]);

        $this->tokenProvider->saveToken($newAccessToken->getToken());
    }

    /**
     * @param string $param
     * @param mixed $default
     * @return mixed
     */
    private function getRequestParam(string $param, $default = null)
    {
        if (!array_key_exists($param, $this->request->getQueryParams())) {
            return $default;
        }

        return $this->request->getQueryParams()[$param];
    }
}
