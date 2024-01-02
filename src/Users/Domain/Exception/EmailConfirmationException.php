<?php

declare(strict_types=1);

namespace App\Users\Domain\Exception;

use RuntimeException;
use Throwable;

class EmailConfirmationException extends RuntimeException
{
    public function __construct(string $message = "", ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
