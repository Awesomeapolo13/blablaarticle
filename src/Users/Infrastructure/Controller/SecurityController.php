<?php

namespace App\Users\Infrastructure\Controller;

use App\Shared\Infrastructure\Security\Authenticator\LoginFormAuthenticator;
use App\Users\Domain\Dictionary\SecurityDictionary;
use App\Users\Infrastructure\Repository\UserRepository;
use App\Users\Infrastructure\ReqHandler\LoginHandler;
use App\Users\Infrastructure\ReqHandler\RegisterHandler;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Psr\Log\LoggerInterface;
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
     *
     * @param Request $request
     * @param GuardAuthenticatorHandler $guard
     * @param LoginFormAuthenticator $authenticator
     * @param EntityManagerInterface $em
     * @param UserRepository $userRepository
     * @param LoggerInterface $emailConfirmLogger - логирование в канал confirm_email
     * @return Response|null
     */
    public function confirmEmail(
        Request                   $request,
        GuardAuthenticatorHandler $guard,
        LoginFormAuthenticator    $authenticator,
        EntityManagerInterface    $em,
        UserRepository            $userRepository,
        LoggerInterface           $emailConfirmLogger
    ): ?Response {

        $confirmationError = 'Некорректная ссылка для подтверждения email. Обратитесь в службу поддержки.';
        // Проверяем корректна ли ссылка, если нет то редирект на регистрацию и вывод ошибки
        if (empty($request->query->get('hash'))) {
            $emailConfirmLogger->error('Некорректная ссылка для подтверждения. Отсутствует параметр hash для подтверждения почты');
            return $this->redirectToRoute('app_register', ['confirmationError' => $confirmationError]);
        }
        $data = json_decode(base64_decode($request->query->get('hash')), true);
        $email = !empty($data['email']) ? $data['email'] : null;
        // Проверяем есть ли необходимые для подтверждения email данные, если нет то редирект на регистрацию и вывод ошибки
        if (!$email) {
            $emailConfirmLogger->error('Некорректная ссылка для подтверждения. Отсутствует параметр email для подтверждения почты');
            return $this->redirectToRoute('app_register', ['confirmationError' => $confirmationError]);
        }

        $user = $userRepository->findOneBy(['email' => $email]);
        // Проверяем есть ли такой пользователь, если нет то редирект на регистрацию и вывод ошибки
        if (!isset($user)) {
            $emailConfirmLogger->error('Пользователь с email ' . $email . ' не проходил регистрацию.');
            return $this->redirectToRoute('app_register', ['confirmationError' => $confirmationError]);
        }
        // Если почта подтверждена - редирект на аутентификацию
        if ($user->getIsEmailConfirmed()) {
            $emailConfirmLogger->info('Пользователь с email ' . $email . ' уже подтвердил свою почту.');
            return $this->redirectToRoute('app_login', ['confirmationError' => 'Пользователь с email ' . $email . ' уже подтвердил свою почту.']);
        }
        // Подтверждаем email
        $user->setIsEmailConfirmed(true);
        $em->persist($user);
        $em->flush();
        // Авторизуем пользователя и делаем редирект на страницу указанную в методе onAuthenticationSuccess аутентификатора
        return $guard
            ->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main'
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
}
