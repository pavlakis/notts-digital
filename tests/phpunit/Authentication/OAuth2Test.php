<?php

namespace NottsDigital\tests\Authentication;

use League\OAuth2\Client\Token\AccessTokenInterface,
    NottsDigital\Authentication\TokenProvider,
    WitteStier\OAuth2\Client\Provider\Meetup,
    PHPUnit\Framework\MockObject\MockObject,
    Psr\Http\Message\ServerRequestInterface,
    NottsDigital\Authentication\OAuth2,
    PHPUnit\Framework\TestCase;

class OAuth2Test extends TestCase
{
    /**
     * @var Meetup|MockObject
     */
    private $provider;

    /**
     * @var TokenProvider
     */
    private $tokenProvider;

    /**
     * @var ServerRequestInterface|MockObject
     */
    private $request;

    protected function setUp(): void
    {
        $this->provider = $this->createMock(Meetup::class);
        $this->tokenProvider = new TokenProvider(TokenProviderTest::TOKEN_FIXTURE_FILENAME);
        $this->request = $this->createMock(ServerRequestInterface::class);
    }

    public static function tearDownAfterClass()
    {
        TokenProviderTest::tearDownAfterClass();
    }

    /**
     * @test
     */
    public function is_authorised_returns_false_on_empty_token(): void
    {
        $oAuth2 = new OAuth2($this->provider, $this->tokenProvider, $this->request);

        static::assertFalse($oAuth2->isAuthorised());
    }

    /**
     * @test
     */
    public function authorise_will_save_token_passed_in_code_query_param(): void
    {
        $this->request->method('getQueryParams')->willReturn([
            'code' => 'the-token',
        ]);
        $oAuth2 = new OAuth2($this->provider, $this->tokenProvider, $this->request);
        $oAuth2->authorise();
        static::assertSame('the-token', $this->tokenProvider->getToken());
    }

    /**
     * @test
     */
    public function authorise_without_code_will_not_update_token(): void
    {
        $this->request->method('getQueryParams')->willReturn([
        ]);
        $oAuth2 = new OAuth2($this->provider, $this->tokenProvider, $this->request);
        $oAuth2->authorise();
        static::assertSame('the-token', $this->tokenProvider->getToken());
    }

    /**
     * @test
     *
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function is_authenticated_returns_false_on_expired_access_token(): void
    {
        $accessToken = $this->createMock(AccessTokenInterface::class);
        $accessToken->method('hasExpired')->willReturn(true);
        $this->provider->method('getAccessToken')->willReturn($accessToken);
        $oAuth2 = new OAuth2($this->provider, $this->tokenProvider, $this->request);

        static::assertFalse($oAuth2->isAuthenticated());
    }

    /**
     * @test
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function refresh_updates_token(): void
    {
        $accessToken = $this->createMock(AccessTokenInterface::class);
        $accessToken->method('getToken')->willReturn('refreshed-token');
        $this->provider->method('getAccessToken')->willReturn($accessToken);
        $oAuth2 = new OAuth2($this->provider, $this->tokenProvider, $this->request);

        $oAuth2->refresh();
        static::assertSame('refreshed-token', $this->tokenProvider->getToken());
    }
}
