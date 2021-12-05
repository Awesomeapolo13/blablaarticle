<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Model\UserRegistrationFormModel;
use App\Form\UserRegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
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
        GuardAuthenticatorHandler    $guard,
        LoginFormAuthenticator       $authenticator,
        EntityManagerInterface       $em
    ): Response
    {
        $form = $this->createForm(UserRegistrationFormType::class);
        // обрабатываем запрос
        $form->handleRequest($request);
        // если форма отправлена и данные ее валидны, начинаем их обработку
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UserRegistrationFormModel $userModel */
            $userModel = $form->getData();
            $user = new User();

            $user
                ->setFirstName($userModel->email)
                ->setEmail($userModel->email)
                ->setPassword($passwordEncoder->encodePassword(
                    $user,
                    $userModel->planePassword
                ))
            ;

            $em->persist($user);
            $em->flush();
            // ToDo: Сделать отправку письма для подтверждения email, Сделать метод для подтверждения email

            // редиректим пользователя на страницу регистрации
            return $guard
                ->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $authenticator,
                    'main'
                );
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
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
