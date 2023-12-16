<?php

namespace App\Users\Application\DTO;

use App\Validator\ConfirmPassword;
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
    public mixed $firstName;

    /**
     * Электронная почта
     *
     * @var string
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public mixed $email;

    /**
     * Пароль
     *
     * @var string
     * @Assert\Length(min="6", minMessage="Минимальная длина пароля 6 символов")
     */
    public mixed $planePassword;

    /**
     * Фабричный метод
     * ToDo Вынести в класс фабрики или маппер.
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
    ): UserRegistrationFormModel {
        $model = new self();

        $model->firstName = $firstName;
        $model->email = $email;
        $model->planePassword = $planePassword;

        return $model;
    }
}
