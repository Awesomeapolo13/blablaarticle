<?php

namespace App\Controller\Admin;

use App\Form\Model\UserRegistrationFormModel;
use App\Form\UserRegistrationFormType;
use App\Repository\UserRepository;
use App\Security\Service\UserDataHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Контроллер отвечающий за работу с профилем пользователя
 *
 * @IsGranted("IS_EMAIL_CONFIRMED", message="Для доступа к этой странице подтвердите электронную почту")
 */
class ProfileController extends AbstractController
{
    /**
     * Отображает страницу профиля пользователя
     *
     * @Route("/admin/profile", name="app_admin_profile")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @throws \Exception
     */
    public function index(
        Request                  $request,
        UserDataHandlerInterface $changeUserDataHandler
    ): Response
    {
        $user = $this->getUser();

        $token = $user->getApiToken();

        $isConfirmed = $request->query->get('isConfirmed');
        // Создаем DTO для передачи в форму
        $userModel = UserRegistrationFormModel::create($user->getFirstName(), $user->getEmail());
        $form = $this->createForm(UserRegistrationFormType::class, $userModel);

        $user = $changeUserDataHandler->handleAndSaveUserData($request, $form, $user);

        $success = isset($user);

        $errors = $form->getErrors(true);
        // Ошибка подтверждения электронной почты
        $confirmationError = $request->query->get('confirmationError');

        return $this->render('admin/profile/index.html.twig', [
            'userForm' => $form->createView(),
            'errors' => $errors,
            'success' => $success,
            'isConfirmed' => $isConfirmed,
            'confirmationError' => $confirmationError,
            'token' => $token
        ]);
    }

    /**
     * Метод подтверждения почты после ее изменения в профиле пользователя
     *
     * @Route("/admin/confirm_email", name="app_admin_profile_confirm_email")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param LoggerInterface $emailConfirmLogger
     * @return RedirectResponse
     * @throws \Exception
     */
    public function confirmEmail(
        Request                $request,
        EntityManagerInterface $em,
        LoggerInterface        $emailConfirmLogger
    ): RedirectResponse
    {
        $redirectAlias = 'app_admin_profile';
        $confirmationError = 'Некорректная ссылка для подтверждения измененного email. Обратитесь в службу поддержки.';

        $user = $this->getUser();
        // Проверяем корректна ли ссылка, если нет то редирект на регистрацию и вывод ошибки
        if (empty($request->query->get('hash'))) {
            $emailConfirmLogger->error('Некорректная ссылка для подтверждения. Отсутствует параметр hash для подтверждения почты');
            return $this->redirectToRoute($redirectAlias, ['confirmationError' => $confirmationError]);
        }

        $data = json_decode(base64_decode($request->query->get('hash')), true);
        $email = !empty($data['email']) ? $data['email'] : null;
        $newEmail = !empty($data['newEmail']) ? $data['newEmail'] : null;
        // Проверяем есть ли необходимые для подтверждения email данные, если нет то редирект на регистрацию и вывод ошибки
        if (!$email || !$newEmail) {
            $emailConfirmLogger->error('Некорректная ссылка для подтверждения. Отсутствует параметр email или newEmail для подтверждения почты');
            return $this->redirectToRoute($redirectAlias, ['confirmationError' => $confirmationError]);
        }

        $session = $request->getSession();
        // Если новая почта пользователя, полученная в url отличается от записанной в сессию, то вывести сообщение об ошибке в профиле
        if ($newEmail !== $session->get('newEmail')) {
            $emailConfirmLogger->error('В сессии отсутствует информация о новой электронной почте пользователя с email ' . $user->getEmail());
            return $this->redirectToRoute($redirectAlias, [
                'confirmationError' => 'Не обнаружено запроса на подтверждение измененной почты. Возможно вы уже ее подтвердили.'
            ]);
        }
        // Если почта пользователя, полученная в url отличается от текущей почты пользователя, то вывести сообщение об ошибке в профиле
        if ($email !== $user->getEmail()) {
            $emailConfirmLogger->error('Нельзя подтвердить email другого пользователя.');
            return $this->redirectToRoute($redirectAlias, ['confirmationError' => 'Нельзя подтвердить email другого пользователя.']);
        }
        // Если почта, указанная в сессии равна текущей почте пользователя, то очищаем сессию
        if ($newEmail === $user->getEmail()) {
            $emailConfirmLogger->info('Почта ' .$newEmail. ' уже подтверждена.');
            $session->remove('newEmail');
            return $this->redirectToRoute($redirectAlias, ['isConfirmed' => true]);
        }

        $user->setEmail($newEmail);
        $em->persist($user);
        $em->flush();
        $session->remove('newEmail');
        $isConfirmed = true;

        return $this->redirectToRoute($redirectAlias, [
            'isConfirmed' => $isConfirmed,
        ]);
    }

    /**
     * Обновляет токен
     *
     * @Route("/admin/update_api_token", name="app_admin_profile_update_api_token")
     *
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function generateNewApiToken(EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();

        if (!isset($user)) {
            $this->json(['message' => 'Такой пользователь не обнаружен пользователь'], 403);
        }

        $token = $user->getApiToken();
        $token->setToken(sha1(uniqid('token', true)));
        $token->setExpiresAt(new \DateTime('+1 day'));
        $em->persist($token);
        $em->flush();

        return $this->json([
            'message' => 'Новый апи токен успешно сгенерирован',
            'token' => $token->getToken(),
            ],
            200,
            [],
            [
                'groups' => ['api_user']
            ]);
    }
}
