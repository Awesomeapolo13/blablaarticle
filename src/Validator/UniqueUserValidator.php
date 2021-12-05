<?php

namespace App\Validator;

use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Валидатор, проверяющий, что не существует пользователя с таким email
 */
class UniqueUserValidator extends ConstraintValidator
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param mixed $value - email, который пользователь ввел в форму регистрации
     * @param Constraint $constraint - считывает конфигурации из класса UniqueUser и применяет их для валидации
     */
    public function validate($value, Constraint $constraint): void
    {
        /* @var $constraint \App\Validator\UniqueUser */

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
