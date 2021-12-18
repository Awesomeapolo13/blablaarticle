<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    /**
     * @Route("/article", name="app_article_demo")
     */
    public function index(): Response
    {
        return $this->render('article/index.html.twig', [
            'show' => false
        ]);
    }
}