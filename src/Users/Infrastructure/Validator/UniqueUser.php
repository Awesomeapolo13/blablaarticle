<?php

declare(strict_types=1);

namespace App\Users\Infrastructure\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Класс конфигурации для валидатора проверки уникальности пользователя по email
 *
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
class UniqueUser extends Constraint
{
    public string $message = 'Пользователь с email"{{ value }}" уже зарегистрирован.';
}
