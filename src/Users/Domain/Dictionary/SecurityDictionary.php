<?php

declare(strict_types=1);

namespace App\Users\Domain\Dictionary;

class SecurityDictionary
{
    public const CONFIRM_EMAIL_TO_FINISH_REGISTER = 'Для завершения регистрации подтвердите ваш email';
    public const CONFIRMATION_EMAIL_FIELD = 'confirmationError';
    public const CONFIRM_EMAIL_ERROR = 'Некорректная ссылка для подтверждения email. Обратитесь в службу поддержки.';
    public const CONFIRM_EMAIL_HASH_IS_EMPTY = 'Некорректная ссылка для подтверждения.'
    . ' Отсутствует параметр hash для подтверждения почты';
    public const CONFIRM_EMAIL_EMPTY_EMAIL = 'Некорректная ссылка для подтверждения.'
    . ' Отсутствует параметр email для подтверждения почты';
    public const CONFIRM_EMAIL_EMPTY_USER = 'Пользователь с email %s не проходил регистрацию.';
    public const CONFIRM_EMAIL_ALREADY_CONFIRMED = 'Пользователь с email %s уже подтвердил свою почту.';
    public const UNDEFINED_USER_MSG = 'Такой пользователь не обнаружен пользователь';
    public const NEW_TOKEN_GENERATED_MSG = 'Новый апи токен успешно сгенерирован';
}
