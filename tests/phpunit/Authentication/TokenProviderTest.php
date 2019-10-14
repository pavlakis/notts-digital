<?php

namespace NottsDigital\tests\Authentication;

use NottsDigital\Authentication\TokenProvider;
use PHPUnit\Framework\TestCase;

class TokenProviderTest extends TestCase
{
    public const TOKEN_FIXTURE_FILENAME = __DIR__ . '/fixtures/.token';

    public static function tearDownAfterClass()
    {
        file_put_contents(self::TOKEN_FIXTURE_FILENAME, '');
    }

    /**
     * @test
     */
    public function token_filename_does_not_exist_throws_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new TokenProvider('nothing');
    }

    /**
     * @test
     */
    public function empty_token_set_as_empty_string(): void
    {
        $token = new TokenProvider(self::TOKEN_FIXTURE_FILENAME);

        static::assertEmpty($token->getToken());
    }

    /**
     * @test
     */
    public function can_save_token(): void
    {
        $token = new TokenProvider(self::TOKEN_FIXTURE_FILENAME);
        $token->saveToken(
            \json_encode(['token' => 'test token', 'refresh_token' => 'refreshed token'])
        );

        static::assertSame('test token', $token->getToken());
        static::assertSame('test token', \json_decode(file_get_contents(self::TOKEN_FIXTURE_FILENAME), true)['token']);
    }
}
