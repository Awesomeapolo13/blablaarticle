<?php

namespace App\Controller\Admin;

use App\Entity\Module;
use App\Form\ModuleFormType;
use App\Repository\ModuleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @param Request $request
     * @param ModuleRepository $moduleRepository
     * @param PaginatorInterface $paginator
     * @param EntityManagerInterface $em
     * @param RoleHierarchyInterface $hierarchy
     * @return Response
     */
    public function index(
        Request            $request,
        ModuleRepository  $moduleRepository,
        PaginatorInterface $paginator,
        EntityManagerInterface $em,
        RoleHierarchyInterface $hierarchy
    ): Response
    {
        /* ToDo: Пока вывожу дефолтные модули. После того как будет готов функционал
             добавления модулей, поправить на модули пользователя.
             Вопросы:
                1) Дефолтные модули должны дополнять те, что пользователь создал функционал?
                Ну то есть при генерации должны использоваться и те и другие? И каким отдавать предпочтение?
        */
        // Получаем пользователя
        $user = $this->getUser();
        // Получаем роли пользователя
        $userRoles = $hierarchy->getReachableRoleNames($user->getRoles());
        // Переменная true, если есть роль администратора
        $isAdmin = in_array("ROLE_ADMIN", $userRoles);
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
            $isAdmin ?: $module->setClient($this->getUser());
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
}
