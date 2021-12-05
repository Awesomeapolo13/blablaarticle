<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints\AbstractComparison;

/**
 * Класс конфигурации для валидатора подтверждения пароля
 *
 * @Annotation
 * @Target({"PROPERTY"})
 */
class ConfirmPassword extends AbstractComparison
{
    /**
     * @var string - сообщение об ошибке
     */
    public $message = 'Пароль не подтвержден';
}
