<?php

namespace App\Controller\Admin;

use App\Repository\ModuleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Контроллер для статей в админском разделе
 *
 * @IsGranted("IS_EMAIL_CONFIRMED", message="Для доступа к этой странице подтвердите электронную почту")
 */
class ModuleController extends AbstractController
{
    /**
     * Отображает страницу истории сгенерированных статей
     *
     * ToDo: после привязки статей к пользователям отображать только статьи,
     *  сгенерированные конкретным пользователем
     *
     * @Route("/admin/module", name="app_admin_module" )
     * @param Request $request
     * @param ModuleRepository $moduleRepository
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(
        Request            $request,
        ModuleRepository  $moduleRepository,
        PaginatorInterface $paginator
    ): Response
    {
        /* ToDo: Пока вывожу дефолтные модули. После того как будет готов функционал
             добавления модулей, поправить на модули пользователя.
             Вопросы:
                1) Дефолтные модули должны дополнять те, что пользователь создал функционал?
                Ну то есть при генерации должны использоваться и те и другие? И каким отдавать предпочтение?
        */

        $paginatedModules = $paginator->paginate(
            $moduleRepository->findAllModulesQuery(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/module/index.html.twig', [
            'modules' => $paginatedModules,
        ]);
    }

    /**
     * Отображает страницу генерации статьи и обрабатывает форму генерации
     *
     * @Route("/admin/article/create", name="app_admin_article_create")
     * @param Request $request
     * @return Response
     */
    public function create(
        Request $request
    ): Response
    {
        //ToDo: Реализовать создание модулей
    }
}
