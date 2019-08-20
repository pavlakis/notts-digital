<?php

namespace NottsDigital\tests\Authentication;

use NottsDigital\Authentication\TokenProvider;
use PHPUnit\Framework\TestCase;

class TokenProviderTest extends TestCase
{
    private static $tokenFilename = __DIR__ . '/fixtures/.token';

    public static function tearDownAfterClass()
    {
        file_put_contents(static::$tokenFilename, '');
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
        $token = new TokenProvider(static::$tokenFilename);

        static::assertEmpty($token->getToken());
    }

    /**
     * @test
     */
    public function can_save_token(): void
    {
        $token = new TokenProvider(static::$tokenFilename);
        $token->saveToken('test token');

        static::assertSame('test token', $token->getToken());
        static::assertSame('test token', file_get_contents(static::$tokenFilename));
    }
}
