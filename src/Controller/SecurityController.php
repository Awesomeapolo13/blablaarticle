<?php

namespace App\Controller;

use App\Entity\User;
use App\Event\UserRegisteredEvent;
use App\Form\Model\UserRegistrationFormModel;
use App\Form\UserRegistrationFormType;
use App\Repository\SubscriptionRepository;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use App\Security\Service\UserDataService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * Отображает страницу авторизации пользователя и выводит оишибки авторизации
     *
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // достает текст последней ошибки авторизации
        $error = $authenticationUtils->getLastAuthenticationError();
        // достает последний логин
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * Отображает страницу с формой регистрации и регистрирует нового пользователя
     *
     * @Route("/register", name="app_register")
     */
    public function register(
        Request                      $request,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface       $em,
        EventDispatcherInterface     $dispatcher,
        SubscriptionRepository       $subscriptionRepository,
        UserDataService              $userDataService
    ): Response
    {
        // переменная для вывода сообщения об успешной регистрации
        $success = false;
        $form = $this->createForm(UserRegistrationFormType::class);
        $user = $userDataService->handleAndSaveUserData($request, $form, $passwordEncoder, $em, $subscriptionRepository);

        if ($user) {
            $dispatcher->dispatch(new UserRegisteredEvent($user));
            $success = true;
        }

        // отдельно достаем ошибки, чтобы отобразить их над формой, параметр true используется для получения
        // ошибок отдельных полей
        $errors = $form->getErrors(true);

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
            'success' => $success,
            'errors' => $errors
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
     * @return Response|null
     * @throws \Exception
     */
    public function confirmEmail(
        Request                   $request,
        GuardAuthenticatorHandler $guard,
        LoginFormAuthenticator    $authenticator,
        EntityManagerInterface    $em,
        UserRepository            $userRepository
    ): ?Response
    {
        // проверяем корректна ли ссылка
        if (empty($request->query->get('hash'))) {
            //ToDo: спросить, что делать, если сгенерирует некорректную ссылку. Быть может стоит при повторной регистрации
            // генерить новую, если пользователь не подтвердил email?
            throw new \Exception('Некорректная ссылка для подтверждение email. Обратитесь в службу поддержки.');
        }
        $email = json_decode(base64_decode($request->query->get('hash')), true)['email'];
        $user = $userRepository->findOneBy(['email' => $email]);
        // проверяем есть ли такой пользователь
        if (!isset($user)) {
            throw new \Exception('Некорректная ссылка для подтверждение email. Такого пользователя не существует');
        }
        // если почта подтверждена - редирект на аутентификацию
        if ($user->getIsEmailConfirmed()) {
            return $this->redirectToRoute('app_login');
        }
        // подтверждаем email
        $user->setIsEmailConfirmed(true);
        $em->persist($user);
        $em->flush();
        // авторизуем пользователя и делаем редирект на страницу указанную в методе onAuthenticationSuccess аутентификатора
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
