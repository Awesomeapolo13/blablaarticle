<?php

namespace App\Controller\Admin;

use App\Entity\Module;
use App\Form\ModuleFormType;
use App\Repository\ModuleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

/**
 * Контроллер для статей в админском разделе
 *
 * @IsGranted("IS_EMAIL_CONFIRMED", message="Для доступа к этой странице подтвердите электронную почту")
 */
class ModuleController extends AbstractController
{
    /**
     * Отображает страницу истории сгенерированных статей и форму создания модуля
     *
     * Создает модули для генерации статьи. Выводит ошибки валидации или об успешном создании модуля.
     * При использовании от администратора, позволяет создавать дефолтные модули, которые используются
     * всеми пользователями портала.
     *
     * @Route("/admin/module", name="app_admin_module" )
     * @IsGranted("IS_PRO_SUBSCRIBER", message="Ваш уровень подписки не позволяет получить доступ к этому функиционалу")
     * @param Request $request
     * @param ModuleRepository $moduleRepository
     * @param PaginatorInterface $paginator
     * @param EntityManagerInterface $em
     * @param RoleHierarchyInterface $hierarchy
     * @return Response
     */
    public function index(
        Request                $request,
        ModuleRepository       $moduleRepository,
        PaginatorInterface     $paginator,
        EntityManagerInterface $em,
        RoleHierarchyInterface $hierarchy
    ): Response {
        $user = $this->getUser();
        // Получаем роли пользователя
        $userRoles = $hierarchy->getReachableRoleNames($user->getRoles());
        // Переменная true, если есть роль администратора
        $isAdmin = in_array("ROLE_ADMIN", $userRoles, true);
        // Если используется администратором, то выводим только дефолтные модули, если нет, то модули пользователя
        $isAdmin
            ?
            $query = $moduleRepository->findAllDefaultModulesQuery()
            :
            $query = $moduleRepository->findModulesByUserQuery($user);

        $paginatedModules = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        $form = $this->createForm(ModuleFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $moduleModel = $form->getData();
            $module = new Module();
            $module
                ->setName($moduleModel->name)
                ->setBody($moduleModel->body)
            ;
            // Добавляем связь с текущим пользователем, если он не администратор
            if (!$isAdmin) {
                $module->setClient($this->getUser());
            }
            // Сохраняем в БД
            $em->persist($module);
            $em->flush();
            // Сообщение об успешном создании модуля
            $this->addFlash('success', 'Модуль успешно добавлен');

            return $this->redirectToRoute('app_admin_module');
        }

        return $this->render('admin/module/index.html.twig', [
            'form' => $form->createView(),
            'errors' => $form->getErrors(true),
            'modules' => $paginatedModules,
        ]);
    }

    /**
     * Удаляет на модуль по его id
     *
     * @Route("admin/module/delete/{id}", name="app_admin_module_delete")
     * @IsGranted("IS_PRO_SUBSCRIBER", message="Ваш уровень подписки не позволяет получить доступ к этому функиционалу")
     * @param int $id - идентификатор модуля для генерации статей
     * @param ModuleRepository $moduleRepository
     * @param LoggerInterface $moduleLogger
     * @param EntityManagerInterface $em
     * @param RoleHierarchyInterface $hierarchy
     * @return JsonResponse
     */
    public function delete(
        int                    $id,
        ModuleRepository       $moduleRepository,
        LoggerInterface        $moduleLogger,
        EntityManagerInterface $em,
        RoleHierarchyInterface $hierarchy
    ): Response {
        $user = $this->getUser();
        $errorMessage = 'Ошибка при удалении модуля. Попробуйте позднее.';
        // Получаем роли пользователя
        $userRoles = $hierarchy->getReachableRoleNames($user->getRoles());
        // Переменная true, если есть роль администратора
        $isAdmin = in_array("ROLE_ADMIN", $userRoles, true);
        // Если в запросе нет id модуля, то пишем лог и редиректим на страницу с модулями
        if (empty($id)) {
            $moduleLogger->error('В запросе не передан идентификатор модуля');
            $this->addFlash('error', $errorMessage);
            return $this->redirectToRoute('app_admin_module');
        }

        $module = $moduleRepository->findOneBy([
            'id' => $id,
            'client' => !$isAdmin ? $user : null,
        ]);
        // Если модуль не обнаружен отправляем сообщение об ошибке и редиректим на страницу с модулями
        if (!isset($module)) {
            $moduleLogger->error('У пользователя не обнаружен модуль с id=' . $id);
            $this->addFlash('error', $errorMessage);
            return $this->redirectToRoute('app_admin_module');
        }

        $module->setDeletedAt(new \DateTime());
        $em->persist($module);
        $em->flush();

        // Сообщение об успешном создании модуля
        $this->addFlash('success', 'Модуль успешно удален');

        return $this->redirectToRoute('app_admin_module');
    }
}
