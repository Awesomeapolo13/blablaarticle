<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Класс конфигурации для валидатора продвигаемого слова
 *
 * @Annotation
 * @Target({"PROPERTY"})
 */
class IsIntegerList extends Constraint
{
    /**
     * @var string - сообщение об ошибке
     */
    public $message = '{{ value }} должно быть числом';
}