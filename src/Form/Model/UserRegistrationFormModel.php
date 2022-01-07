<?php

namespace App\Form\Model;

use App\Validator\ConfirmPassword;
use App\Validator\UniqueUser;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Класс DTO для формы регистрации пользователя
 */
class UserRegistrationFormModel
{
    /**
     * @var string - имя нового пользователя
     * @Assert\NotBlank(message="Заполните поле имя")
     * @Assert\Length(min="2", minMessage="Минимальная длина имени 2 символа")
     */
    public $firstName;

    /**
     * @var string - электронная почта
     * @Assert\NotBlank()
     * @Assert\Email()
     * @UniqueUser()
     */
    public $email;

    /**
     * @var string - пароль
     * @Assert\NotBlank(message="Введите пароль")
     * @Assert\Length(min="6", minMessage="Минимальная длина пароля 6 символов")
     */
    public $planePassword;

    /**
     * @var string - пароль для подтверждения
     * @Assert\NotBlank(message="Введите пароль")
     * @ConfirmPassword(propertyPath="planePassword")
     */
    public $confirmPassword;
}
