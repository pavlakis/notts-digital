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
     */
    public function isAuthenticated(): bool
    {
        try {
            return !$this->getAccessToken()->hasExpired();
        } catch (IdentityProviderException $e) {
            return false;
        }
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
     * @throws IdentityProviderException
     */
    public function authorise(): void
    {
        if (null !== $this->getRequestParam('code')) {
            $accessToken = $this->provider->getAccessToken('authorization_code', [
                'code' => $this->getRequestParam('code')
            ]);

            $this->saveToken($this->getRequestParam('code'),  $accessToken->getRefreshToken());
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
        $existingRefreshToken = $this->provider->getAccessToken('refresh_token', [
            'refresh_token' => $this->tokenProvider->getRefreshToken()
        ]);

        $newAccessToken = $this->provider->getAccessToken('refresh_token', [
            'refresh_token' => $existingRefreshToken->getRefreshToken()
        ]);

        $this->saveToken($newAccessToken->getToken(), $newAccessToken->getRefreshToken());
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

    private function saveToken(string $accessToken, string $refreshToken): void
    {
        $token = \json_encode([
            'token' => $accessToken,
            'refresh_token' => $refreshToken,
        ]);

        $this->tokenProvider->saveToken($token);
    }
}
