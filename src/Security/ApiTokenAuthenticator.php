<?php

namespace App\Security;

use App\Repository\ApiTokenRepository;
use App\Users\Domain\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * Аутентификатор для доступа по api токену
 */
class ApiTokenAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var ApiTokenRepository
     */
    private $apiTokenRepository;

    public function __construct(ApiTokenRepository $apiTokenRepository)
    {
        $this->apiTokenRepository = $apiTokenRepository;
    }

    /**
     * Проверяет наличие заголовка Authorization и Bearer токена
     *
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request): bool
    {
        return $request->headers->has('Authorization') && 0 === strpos(
            $request->headers->get('Authorization'), 'Bearer ' // ToDo: Заменить на использование регулярного выражения
            );
    }

    /**
     * Получает токен без приставки Bearer
     *
     * @param Request $request
     * @return false|string
     */
    public function getCredentials(Request $request)
    {
        // ToDo: Заменить на использование регулярного выражения
        return substr($request->headers->get('Authorization'), 7);
    }

    /**
     * Получает пользователя по токену и проверяет не истек ли его токен
     *
     * @param $credentials
     * @param UserProviderInterface $userProvider
     * @return User|UserInterface|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = $this->apiTokenRepository->findOneBy(['token' => $credentials]);

        if (!isset($token)) {
            throw new CustomUserMessageAuthenticationException('Invalid token');
        }
        // Проверяем время жизни токена
        if ($token->isExpired()) {
            throw new CustomUserMessageAuthenticationException('Token expired');
        }

        return $token->getClient();
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return true;
    }

    /**
     * Возвращает ошибку при неудачной авторизации
     *
     * @param Request $request
     * @param AuthenticationException $exception
     * @return JsonResponse
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        return new JsonResponse([
            'message' => $exception->getMessage(),
        ],
            401
        );
    }

    /**
     * Метод просто позволяет попасть дальше в контроллер, при успешной аутентификации ничего не возвращает
     *
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return void
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        // continue
    }

    /**
     * Никогда не выполняется, т.к. основная точка входа в приложение - форма аутентификации, а не токен
     *
     * Вызывается метод start аутентификатора по логину и паролю
     *
     * @param Request $request
     * @param AuthenticationException|null $authException
     * @return void
     * @throws \Exception
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        throw new \Exception('Never called');
    }

    /**
     * Функционал "Запомни меня" не поддерживаем, поэтому возвращаем false
     *
     * @return bool
     */
    public function supportsRememberMe(): bool
    {
        return false;
    }
}
