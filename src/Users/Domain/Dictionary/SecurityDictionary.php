<?php

declare(strict_types=1);

namespace App\Users\Domain\Dictionary;

class SecurityDictionary
{
    public const CONFIRM_EMAIL_TO_FINISH_REGISTER = 'Для завершения регистрации подтвердите ваш email';
    public const CONFIRMATION_EMAIL_FIELD = 'confirmationError';
    public const NEW_EMAIL_SESS_KEY = 'newEmail';
    public const CONFIRM_EMAIL_ERROR = 'Некорректная ссылка для подтверждения email. Обратитесь в службу поддержки.';
    public const CONFIRM_EMAIL_HASH_IS_EMPTY = 'Некорректная ссылка для подтверждения.'
    . ' Отсутствует параметр hash для подтверждения почты';
    public const CONFIRM_EMAIL_EMPTY_EMAIL = 'Некорректная ссылка для подтверждения.'
    . ' Отсутствует параметр email для подтверждения почты';
    public const CONFIRM_ADMIN_EMPTY_EMAIL = 'Некорректная ссылка для подтверждения.'
    . ' Отсутствует параметр email или newEmail для подтверждения почты';
    public const CONFIRM_EMAIL_EMPTY_USER = 'Пользователь с email %s не проходил регистрацию.';
    public const CONFIRM_EMAIL_ALREADY_CONFIRMED = 'Пользователь с email %s уже подтвердил свою почту.';
    public const CONFIRM_EMAIL_EMPTY_NEW_EMAIL = 'В сессии отсутствует информация о'
    . ' новой электронной почте пользователя с email %s';
    public const CONFIRM_EMAIL_IS_NOT_EXISTS = 'Не обнаружено запроса на подтверждение измененной почты.'
    . ' Возможно вы уже ее подтвердили.';
    public const CONFIRM_EMAIL_NOT_YOUR_EMAIL = 'Нельзя подтвердить email другого пользователя.';
    public const CONFIRM_EMAIL_ALREADY_CONFIRMED_NEW_EMAIL = 'Почта %s уже подтверждена.';
    public const UNDEFINED_USER_MSG = 'Такой пользователь не обнаружен пользователь';
    public const NEW_TOKEN_GENERATED_MSG = 'Новый апи токен успешно сгенерирован';
}
