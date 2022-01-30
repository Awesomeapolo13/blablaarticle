<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints\AbstractComparisonValidator;

/**
 * Валидатор диапазона модулей для генерации статьи
 */
class SizeRangeValidator extends AbstractComparisonValidator
{
    /**
     * Проверка на корректность диапазона модулей статьи
     *
     * Если оба поля заполнены, то будет проверять корректность указанных диапазонов
     *
     * @param mixed $sizeFrom - минимальное количество модулей для генерации статьи
     * @param mixed $sizeTo - максимальное количество модулей для генерации статьи
     * @return bool - вернет true, если максимальное количество модулей больше или равно минимальному
     */
    protected function compareValues($sizeFrom, $sizeTo): bool
    {
        return !(!empty($sizeFrom) && !empty($sizeTo)) || $sizeTo >= $sizeFrom;
    }
}