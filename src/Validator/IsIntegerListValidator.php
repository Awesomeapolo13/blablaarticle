<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Валидатор простого списка чисел
 */
class IsIntegerListValidator extends ConstraintValidator
{
    /**
     * Проверка на то что параметр является списком чисел
     *
     * @param array $value
     * @param Constraint $constraint
     * @return void
     */
    public function validate($value, Constraint $constraint): void
    {
        /** @var $constraint IsIntegerList */

        foreach ($value as $item) {
            if (!is_int($item)) {
                return;
            }
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', 'Количество повторений продвигаемого слова')
            ->addViolation()
        ;
    }
}