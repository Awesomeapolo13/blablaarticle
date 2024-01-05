<?php

declare(strict_types=1);

namespace App\Users\Domain\ValueObject;

readonly class ApiTokenVO
{
    private const PREFIX = 'token';

    private string $token;

    public function __construct(?string $token = null)
    {
        $this->token = is_null($token)
            ? sha1(uniqid(self::PREFIX, true))
            : $token;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
