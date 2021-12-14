<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints\AbstractComparisonValidator;

/**
 * Валидатор соответствия пароля введенному
 */
class ConfirmPasswordValidator extends AbstractComparisonValidator
{
    /**
     * Проверка соответствия введенного пароля подтвержденному
     *
     * @param mixed $planePassword - введенный пароль
     * @param mixed $confirmPassword - пароль для подтверждения введенного
     * @return bool - если true, то валидация пройдена успешно
     */
    protected function compareValues($planePassword, $confirmPassword): bool
    {
        return $confirmPassword === $planePassword;
    }
}
