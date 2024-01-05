<?php

declare(strict_types=1);

namespace App\Users\Domain\Dictionary;

class ProfileDictionary
{
    public const EXPIRED_TOKEN_MSG = 'Срок действия Вашего токена истек.'
    . ' Выполните генерацию нового токена для продолжения работы с API';
}
