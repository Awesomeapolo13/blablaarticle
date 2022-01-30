<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints\AbstractComparison;

/**
 * Класс конфигурации для валидатора продвигаемого слова
 *
 * @Annotation
 * @Target({"PROPERTY"})
 */
class IsEmptyBoth extends AbstractComparison
{
    /**
     * @var string - сообщение об ошибке
     */
    public $message = 'Необходимо указать и продвинутое слово и количество его повторений ';
}