<?php

declare(strict_types=1);

namespace App\Users\Infrastructure\Validator;

use App\Users\Infrastructure\Repository\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Валидатор, проверяющий, что не существует пользователя с таким email
 */
class UniqueUserValidator extends ConstraintValidator
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    /**
     * @param mixed $value - email, который пользователь ввел в форму регистрации
     * @param Constraint $constraint - считывает конфигурации из класса UniqueUser и применяет их для валидации
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var $constraint UniqueUser */

        if (null === $value || '' === $value) {
            return;
        }

        // если не удалось найти пользователя с таким email, прерываем проверку валидации
        if (!$this->userRepository->findOneBy(['email' => $value])) {
            return;
        }
        // иначе выводим ошибку
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}
