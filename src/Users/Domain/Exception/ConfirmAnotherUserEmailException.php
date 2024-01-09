<?php

declare(strict_types=1);

namespace App\Users\Domain\Exception;

use App\Users\Domain\Dictionary\SecurityDictionary;
use Throwable;

class ConfirmAnotherUserEmailException extends EmailConfirmationException
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct(SecurityDictionary::CONFIRM_EMAIL_NOT_YOUR_EMAIL, $previous);
    }
}
