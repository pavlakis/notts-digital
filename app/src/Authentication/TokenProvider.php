<?php

namespace NottsDigital\Authentication;

final class TokenProvider implements TokenProviderInterface
{
    /** @var string */
    private $token;

    /** @var string */
    private $tokenFilename;

    public function __construct(string $tokenFilename)
    {
        if (!file_exists($tokenFilename)) {
            throw new \InvalidArgumentException('Token does not exist.');
        }

        $this->tokenFilename = $tokenFilename;
        $this->token = (string) file_get_contents($tokenFilename);
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function saveToken(string $token): void
    {
        $this->token = $token;
        file_put_contents($this->tokenFilename, $this->token);
    }
}
