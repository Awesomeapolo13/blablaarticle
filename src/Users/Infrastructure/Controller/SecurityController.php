<?php

namespace App\Users\Infrastructure\Controller;

use App\Shared\Domain\Dictionary\GuardDictionary;
use App\Shared\Infrastructure\Security\Authenticator\LoginFormAuthenticator;
use App\Users\Domain\Dictionary\SecurityDictionary;
use App\Users\Domain\Exception\AlreadyConfirmedEmailException;
use App\Users\Domain\Exception\EmptyEmailConfirmException;
use App\Users\Infrastructure\ReqHandler\ConfirmEmailHandler;
use App\Users\Infrastructure\ReqHandler\LoginHandler;
use App\Users\Infrastructure\ReqHandler\RegisterHandler;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class SecurityController extends AbstractController
{
    /**
     * Отображает страницу авторизации пользователя и выводит ошибки авторизации
     *
     * @Route("/login", name="app_login")
     */
    public function login(Request $request, LoginHandler $loginHandler): Response
    {
        return $this->render(
            'security/login.html.twig',
            $loginHandler->handleLogin($request)
        );
    }

    /**
     * Отображает страницу с формой регистрации и регистрирует нового пользователя
     *
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, RegisterHandler $registerHandler): Response
    {
        /**
         * Тест для регистрации:
         *  1) При первом открытии страницы
         *      - success: false,
         *      - registrationForm: FormView,
         *      - errors: FromErrorInterface|emptyCollection,
         *      - confirmationError: null.
         *  2) При введении некорректных данных (класс формы протестировать отдельно):
         *      - success: false,
         *      - registrationForm: FormView,
         *      - errors: FromErrorInterface|nonEmpty,
         *      - confirmationError: null.
         *  3) П.2 проверить для каждого поля формы.
         *  4) Ошибку подтверждения почты проверить отдельно.
         *  5) После успеха проверить создался ли пользователь с указанным email.
         */
        $response = $registerHandler->handleRegister($request);
        if (isset($response['success']) && $response['success']) {
            $this->addFlash('success', SecurityDictionary::CONFIRM_EMAIL_TO_FINISH_REGISTER);
        }

        return $this->render(
            'security/register.html.twig',
            $response
        );
    }

    /**
     * Метод подтверждения email для завершения регистрации
     *
     * Обращение к методу идет при переходе по ссылке для подтверждения email.
     * Расшифровывается гет параметр, содержащий почту, находится пользователь, затем подтверждается email
     * и он авторизуется
     *
     * @Route("/confirm_email", name="app_confirm_email")
     */
    public function confirmEmail(
        Request                   $request,
        GuardAuthenticatorHandler $guard,
        LoginFormAuthenticator    $authenticator,
        ConfirmEmailHandler $confirmEmailHandler
    ): ?Response {
        try {
            $user = $confirmEmailHandler->confirmEmail($request->query->get('hash'));
        } catch (AlreadyConfirmedEmailException $exception) {
            return $this->redirectToLogin($exception->getMessage());
        } catch (EmptyEmailConfirmException $exception) {
            return $this->redirectToRegistration();
        }

        // Авторизуем пользователя и делаем редирект на страницу указанную в методе onAuthenticationSuccess аутентификатора
        return $guard
            ->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                GuardDictionary::MAIN_PROVIDER_KEY
            );
    }

    /**
     * Отвечает за выход пользователя
     *
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    private function redirectToRegistration(): Response
    {
        return $this->redirectToRoute(
            'app_register',
            ['confirmationError' => SecurityDictionary::CONFIRM_EMAIL_ERROR]
        );
    }

    private function redirectToLogin(string $confirmationError): Response
    {
        return $this->redirectToRoute('app_login', ['confirmationError' => $confirmationError]);
    }
}
