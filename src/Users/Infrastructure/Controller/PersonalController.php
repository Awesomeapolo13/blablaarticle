<?php

namespace App\Users\Infrastructure\Controller;

use App\Users\Infrastructure\Service\Dashboard\UserDashboardService;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PersonalController extends AbstractController
{
    /**
     * Отображает рабочий стол пользователя
     *
     * @Route("/admin/personal", name="app_admin_personal")
     * @throws NonUniqueResultException
     */
    public function getDashboardAction(UserDashboardService $dashboardService,): Response
    {
        return $this->render(
            'admin/personal/index.html.twig',
            $dashboardService->getDashboard($this->getUser())
        );
    }
}
