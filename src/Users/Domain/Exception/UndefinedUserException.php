<?php

declare(strict_types=1);

namespace App\Users\Domain\Exception;

use App\Shared\Infrastructure\Http\RespCodeDictionary;
use App\Users\Domain\Dictionary\SecurityDictionary;
use RuntimeException;
use Throwable;

class UndefinedUserException extends RuntimeException
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct(
            SecurityDictionary::UNDEFINED_USER_MSG,
            RespCodeDictionary::FORBIDDEN_CODE,
            $previous
        );
    }
}
