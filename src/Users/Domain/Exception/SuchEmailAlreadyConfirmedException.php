<?php

declare(strict_types=1);

namespace App\Users\Domain\Exception;

use Throwable;

class SuchEmailAlreadyConfirmedException extends EmailConfirmationException
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct($message, $previous);
    }
}
