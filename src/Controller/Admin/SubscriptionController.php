<?php

namespace App\Controller\Admin;

use App\Repository\SubscriptionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionController extends AbstractController
{
    /**
     * Отображает страницу с информацией о подписке пользователя
     *
     * @Route("/admin/subscription", name="app_admin_subscription" )
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @param SubscriptionRepository $subscriptionRepository
     * @return Response
     */
    public function index(SubscriptionRepository $subscriptionRepository): Response
    {
        /**
         * ToDo:
         *      1) Выполнить вывод всех подписок из базы на страницу и ограничить доступ к админке для неавторизованных
         *          пользователей
         *      2) Реализовать механизм изменения подписки в соостветствии с ТЗ
         */
        $user = $this->getUser();

        $subscriptions = $subscriptionRepository->findAll();

        return $this->render('admin/subscription/index.html.twig', [
            'subscriptions' => $subscriptions,
            'userSubscriptionId' => ($user->getSubscription())->getId(),
        ]);
    }
}