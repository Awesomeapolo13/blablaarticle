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
     * Имя нового пользователя
     *
     * @var string
     * @Assert\NotBlank(message="Заполните поле имя")
     * @Assert\Length(min="2", minMessage="Минимальная длина имени 2 символа")
     */
    public $firstName;

    /**
     * Электронная почта
     *
     * @var string
     * @Assert\NotBlank()
     * @Assert\Email()
     * @UniqueUser()
     */
    public $email;

    /**
     * Пароль
     *
     * @var string
     * @Assert\NotBlank(message="Введите пароль")
     * @Assert\Length(min="6", minMessage="Минимальная длина пароля 6 символов")
     */
    public $planePassword;

    /**
     * Повторенный пароль для подтверждения
     *
     * @var string
     * @Assert\NotBlank(message="Введите пароль")
     * @ConfirmPassword(propertyPath="planePassword")
     */
    public $confirmPassword;

    /**
     * Фабричный метод
     *
     * @param string $firstName
     * @param string $email
     * @param string $planePassword
     * @return UserRegistrationFormModel
     */
    public static function create(
        string $firstName,
        string $email,
        string $planePassword = ''
    )
    {
        $model = new self();

        $model->firstName = $firstName;
        $model->email = $email;
        $model->planePassword = $planePassword;
        $model->confirmPassword = $planePassword;

        return $model;
    }
}
