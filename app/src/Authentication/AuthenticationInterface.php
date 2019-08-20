<?php

namespace NottsDigital\Authentication;

interface AuthenticationInterface
{
    public function isAuthorised(): bool;

    public function isAuthenticated(): bool;

    public function authorise(): void;

    public function refresh(): void;
}
