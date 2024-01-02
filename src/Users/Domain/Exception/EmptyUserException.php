<?php

declare(strict_types=1);

namespace App\Users\Domain\Exception;

use App\Users\Domain\Dictionary\SecurityDictionary;
use Throwable;

class EmptyUserException extends EmailConfirmationException
{
    public function __construct(string $email, ?Throwable $previous = null)
    {
        parent::__construct(
            sprintf(SecurityDictionary::CONFIRM_EMAIL_EMPTY_USER, $email),
            $previous)
        ;
    }
}
