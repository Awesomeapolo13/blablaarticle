<?php

declare(strict_types=1);

namespace App\Users\Domain\Exception;

use App\Users\Domain\Dictionary\SecurityDictionary;
use Throwable;

class EmptyEmailConfirmException extends EmailConfirmationException
{
    public function __construct(?string $message = null, ?Throwable $previous = null)
    {
        parent::__construct($message ?? SecurityDictionary::CONFIRM_EMAIL_EMPTY_EMAIL, $previous);
    }
}