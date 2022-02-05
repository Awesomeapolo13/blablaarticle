<?php

namespace App\Controller\Admin;

use App\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
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
     * @param Request $request
     * @param SubscriptionRepository $subscriptionRepository
     * @return Response
     */
    public function index(
        Request                $request,
        SubscriptionRepository $subscriptionRepository
    ): Response
    {
        $user = $this->getUser();

        $subscriptions = $subscriptionRepository->findSubscriptionsOrderedByPrice();

        return $this->render('admin/subscription/index.html.twig', [
            'subscriptions' => $subscriptions,
            'userSubscription' => $user->getSubscription(),
            'expireAt' => $user->getExpireAt(),
            'change' => $request->query->get('change'),
        ]);
    }

    /**
     * Меняет подписку у пользователя
     *
     * @Route("/admin/subscription/{id}", name="app_admin_subscription_change")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @param int $id - идентификатор подписки
     * @param EntityManagerInterface $em
     * @param SubscriptionRepository $subscriptionRepository
     * @return Response
     */
    public function changeSubscription(
        int                    $id,
        EntityManagerInterface $em,
        SubscriptionRepository $subscriptionRepository
    ): Response
    {
        $success = false;
        $user = $this->getUser();
        $newSubscription = $subscriptionRepository->findOneBy(['id' => $id]);

        // нельзя понизить текущий уровень подписки
        if (($user->getSubscription())->getPrice() < $newSubscription->getPrice()) {
            $user
                ->setSubscription($newSubscription)
                ->setExpireAt(new \DateTime('+1 week'))
            ;

            $em->persist($user);
            $em->flush();

            $success = true;
        }

        return $this->redirectToRoute('app_admin_subscription', [
            'change' => $success,
        ]);
    }
}