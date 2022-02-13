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
     */
    public $email;

    /**
     * Пароль
     *
     * @var string
     * @Assert\Length(min="6", minMessage="Минимальная длина пароля 6 символов")
     */
    public $planePassword;

    /**
     * Повторенный пароль для подтверждения
     *
     * @var string
     * @ConfirmPassword(propertyPath="planePassword")
     * @Assert\Expression(
     *     "this.planePassword === this.confirmPassword",
     *     message="Пароль не соответствует введенному"
     * )
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
    ): UserRegistrationFormModel
    {
        $model = new self();

        $model->firstName = $firstName;
        $model->email = $email;
        $model->planePassword = $planePassword;
        $model->confirmPassword = $planePassword;

        return $model;
    }
}
