<?php

declare(strict_types=1);

namespace App\Users\Infrastructure\ReqHandler;

use App\Users\Domain\Dictionary\SecurityDictionary;
use App\Users\Domain\Entity\User;
use App\Users\Domain\Exception\ConfirmAnotherUserEmailException;
use App\Users\Domain\Exception\EmptyEmailConfirmException;
use App\Users\Domain\Exception\EmptyEmailConfirmHashException;
use App\Users\Domain\Exception\EmptyNewEmailException;
use App\Users\Domain\Exception\SuchEmailAlreadyConfirmedException;
use App\Users\Domain\Repository\UserRepositoryInterface;
use App\Users\Domain\Service\EmailConfirmHashDecoder;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AdminConfirmEmailHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly LoggerInterface $emailConfirmLogger
    ) {
    }

    public function confirmEmail(string $hash, UserInterface $user, ?string $sessNewEmail): void
    {
        if (empty($hash)) {
            $this->emailConfirmLogger->error(SecurityDictionary::CONFIRM_EMAIL_HASH_IS_EMPTY);
            throw new EmptyEmailConfirmHashException();
        }

        $data = EmailConfirmHashDecoder::decode($hash);
        $email = !empty($data['email']) ? $data['email'] : null;
        $newEmail = !empty($data['newEmail']) ? $data['newEmail'] : null;

        // Проверяем есть ли необходимые для подтверждения email данные, если нет то редирект на регистрацию и вывод ошибки
        if (!$email || !$newEmail) {
            $this->emailConfirmLogger->error(SecurityDictionary::CONFIRM_ADMIN_EMPTY_EMAIL);
            throw new EmptyEmailConfirmException(SecurityDictionary::CONFIRM_ADMIN_EMPTY_EMAIL);
        }
        // Если новая почта пользователя, полученная в url отличается от записанной в сессию,
        // то вывести сообщение об ошибке в профиле.
        if ($newEmail !== $sessNewEmail) {
            $this->emailConfirmLogger->error(
                sprintf(SecurityDictionary::CONFIRM_EMAIL_EMPTY_NEW_EMAIL, $user->getEmail())
            );
            throw new EmptyNewEmailException();
        }
        // Если почта пользователя, полученная в url отличается от текущей почты пользователя, то вывести сообщение об ошибке в профиле
        if ($email !== $user->getEmail()) {
            $this->emailConfirmLogger->error(SecurityDictionary::CONFIRM_EMAIL_NOT_YOUR_EMAIL);
            throw new ConfirmAnotherUserEmailException();
        }
        // Если почта, указанная в сессии равна текущей почте пользователя, то очищаем сессию
        if ($newEmail === $user->getEmail()) {
            $this->emailConfirmLogger->info(
                sprintf(SecurityDictionary::CONFIRM_EMAIL_ALREADY_CONFIRMED_NEW_EMAIL, $newEmail)
            );
            throw new SuchEmailAlreadyConfirmedException();
        }

        $user->setEmail($newEmail);
        $this->userRepository->save($user);
    }
}
