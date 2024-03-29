<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserRegistrationFormType;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use App\Security\Service\UserDataHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * Отображает страницу авторизации пользователя и выводит ошибки авторизации
     *
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        // достает текст последней ошибки авторизации
        $error = $authenticationUtils->getLastAuthenticationError();
        $confirmationError = $request->query->get('confirmationError');
        // достает последний логин
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'confirmationError' => $confirmationError,
        ]);
    }

    /**
     * Отображает страницу с формой регистрации и регистрирует нового пользователя
     *
     * @Route("/register", name="app_register")
     */
    public function register(
        Request                  $request,
        UserDataHandlerInterface $registrationDataHandler
    ): Response {
        $form = $this->createForm(UserRegistrationFormType::class);
        $user = new User();
        $user = $registrationDataHandler->handleAndSaveUserData($request, $form, $user);
        $success = isset($user);
        if ($success) {
            $this->addFlash('success', 'Для завершения регистрации подтвердите ваш email');
        }

        // Сообщение об ошибке при подтверждении email
        $confirmationError = $request->query->get('confirmationError');

        // отдельно достаем ошибки, чтобы отобразить их над формой, параметр true используется для получения
        // ошибок отдельных полей
        $errors = $form->getErrors(true);

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
            'success' => $success,
            'errors' => $errors,
            'confirmationError' => $confirmationError,
        ]);
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
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
