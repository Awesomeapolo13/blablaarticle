<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints\AbstractComparisonValidator;

/**
 * Валидатор заполненности двух связанных полей типа CollectionType
 */
class IsEmptyBothValidator extends AbstractComparisonValidator
{
    /**
     * Проверка на заполненность обоих полей
     *
     * @param array $value1
     * @param array $value2
     * @return bool - вернет true, если заполнены оба поля
     */
    protected function compareValues($value1, $value2): bool
    {
        foreach ($value1 as $key => $item) {
            if (empty($value2[$key]) || empty($item)) {
                return false;
            }
        }

        return true;
    }
}