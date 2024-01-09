<?php

declare(strict_types=1);

namespace App\Users\Domain\Service;

final class EmailConfirmHashDecoder
{
    public static function decode(string $hash): array
    {
        return json_decode(base64_decode($hash), true);
    }
}
