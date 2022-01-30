<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints\AbstractComparison;

/**
 * Класс конфигурации для валидатора диапазона модулей для генерации статьи
 *
 * @Annotation
 * @Target({"PROPERTY"})
 */
class SizeRange extends AbstractComparison
{
    /**
     * @var string - сообщение об ошибке
     */
    public $message = 'Минимальное количество модулей не может превышать максимальное';
}