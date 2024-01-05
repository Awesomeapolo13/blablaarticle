<?php

declare(strict_types=1);

namespace App\Users\Infrastructure\ReqHandler;

use App\Users\Domain\Dictionary\SecurityDictionary;
use App\Users\Domain\Entity\User;
use App\Users\Domain\Exception\AlreadyConfirmedEmailException;
use App\Users\Domain\Exception\EmptyEmailConfirmException;
use App\Users\Domain\Exception\EmptyEmailConfirmHashException;
use App\Users\Domain\Exception\EmptyUserException;
use App\Users\Domain\Repository\UserRepositoryInterface;
use Psr\Log\LoggerInterface;

class ConfirmEmailHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly LoggerInterface $emailConfirmLogger
    ) {
    }

    public function confirmEmail(string $hash): User
    {
        if (empty($hash)) {
            $this->emailConfirmLogger->error(SecurityDictionary::CONFIRM_EMAIL_HASH_IS_EMPTY);
            throw new EmptyEmailConfirmHashException();
        }
        $data = json_decode(base64_decode($hash), true);
        $email = !empty($data['email']) ? $data['email'] : null;
        // Проверяем есть ли необходимые для подтверждения email данные, если нет то редирект на регистрацию и вывод ошибки
        if (!$email) {
            $this->emailConfirmLogger->error(SecurityDictionary::CONFIRM_EMAIL_EMPTY_EMAIL);
            throw new EmptyEmailConfirmException();
        }

        $user = $this->userRepository->findOneBy(['email' => $email]);
        // Проверяем есть ли такой пользователь, если нет то редирект на регистрацию и вывод ошибки
        if (!isset($user)) {
            $this->emailConfirmLogger->error(sprintf(SecurityDictionary::CONFIRM_EMAIL_EMPTY_USER, $email));
            throw new EmptyUserException($email);
        }
        // Если почта подтверждена - редирект на аутентификацию
        if ($user->getIsEmailConfirmed()) {
            $this->emailConfirmLogger->info(sprintf(SecurityDictionary::CONFIRM_EMAIL_ALREADY_CONFIRMED, $email));
            throw new AlreadyConfirmedEmailException($email);
        }

        $this->userRepository->save($user);

        return $user;
    }
}
