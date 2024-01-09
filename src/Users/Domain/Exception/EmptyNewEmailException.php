<?php

declare(strict_types=1);

namespace App\Users\Domain\Exception;

use App\Users\Domain\Dictionary\SecurityDictionary;
use Throwable;

class EmptyNewEmailException extends EmailConfirmationException
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct(SecurityDictionary::CONFIRM_EMAIL_IS_NOT_EXISTS, $previous);
    }
}
