<?php

namespace App\Security\Service\UserDataHandler;

use App\Event\UserChangeEmailEvent;
use App\Form\Model\UserRegistrationFormModel;
use App\Security\Service\UserDataHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Обработчик данных пользователя при изменении через профиль
 */
class ChangeUserDataHandler implements UserDataHandlerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(
        EntityManagerInterface       $em,
        EventDispatcherInterface     $dispatcher,
        UserPasswordEncoderInterface $passwordEncoder,
    )
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
        $this->passwordEncoder = $passwordEncoder;
    }


    /**
     * Обработка пользовательских данных при их изменении в профиле пользователя
     *
     * @param Request $request - запрос, содержащий данные из формы для обработки
     * @param FormInterface $form - форма для обработки и валидации данных
     * @param UserInterface $user - сущность пользователя для сохранения в БД
     * @return UserInterface|void - вернет объект пользователя, либо null, если форма не валидна
     */
    public function handleAndSaveUserData(Request $request, FormInterface $form, UserInterface $user)
    {
        $form->handleRequest($request);
        // если форма отправлена и данные ее валидны, начинаем их обработку
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UserRegistrationFormModel $userModel */
            $userModel = $form->getData();
            $newPassword = !empty($userModel->planePassword)
                ?
                $this->passwordEncoder->encodePassword($user, $userModel->planePassword)
                :
                '';

            $user->setFirstName($userModel->firstName);

            // Проверяем изменен ли пароль
            if (!empty($newPassword)) {
                $user->setPassword($newPassword);
            }
            // Сохраняем данные пользователя
            $this->em->persist($user);
            $this->em->flush();

            // Проверяем изменена ли электронная почта и если да, то отправляем ссылку для ее подтверждения
            if ($user->getEmail() !== $userModel->email) {
                // записываем email в сессию для его записи в БД после подтверждения
                ($request->getSession())->set('newEmail', $userModel->email);
                $this->dispatcher->dispatch(new UserChangeEmailEvent($user, $userModel->email));
            }

            return $user;
        }
    }
}
