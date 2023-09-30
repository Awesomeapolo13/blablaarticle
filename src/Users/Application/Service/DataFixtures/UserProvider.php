<?php

declare(strict_types=1);

namespace App\Users\Application\Service\DataFixtures;

class UserProvider
{
    public const ADMIN_USER_PLUS = [
        'firstName' => 'Petrusha',
        'email' => 'plusSubscriber@mail.ru',
        'roles' => ['ROLE_ADMIN'],
    ];
    public const ADMIN_USER_PRO = [
        'firstName' => 'Nadezhda',
        'email' => 'proSubscriber@mail.ru',
        'roles' => ['ROLE_ADMIN'],
    ];
}
