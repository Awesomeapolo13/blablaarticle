<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Класс конфигурации для валидатора проверки уникальности пользователя по email
 *
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
class UniqueUser extends Constraint
{
    /**
     * @var string - сообщение об ошибке
     */
    public $message = 'Пользователь с email"{{ value }}" уже зарегистрирован.';
}
