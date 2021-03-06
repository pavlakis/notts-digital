<?php

namespace NottsDigital\Authentication;

interface TokenProviderInterface
{
    public function getToken(): string;

    public function getRefreshToken(): string;

    public function saveToken(string $token): void;
}
