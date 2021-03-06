<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PersonalController extends AbstractController
{
    /**
     * Отображает рабочий стол пользователя (ToDo: после реализации убрать заглушку)
     *
     * @Route("/admin/personal", name="app_admin_personal")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }
}
