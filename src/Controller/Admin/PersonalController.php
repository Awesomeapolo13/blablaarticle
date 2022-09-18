<?php

namespace App\Controller\Admin;

use App\Repository\ArticleRepository;
use Carbon\Carbon;
use Doctrine\ORM\NonUniqueResultException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;

class PersonalController extends AbstractController
{
    /**
     * Отображает рабочий стол пользователя
     * @Route("/admin/personal", name="app_admin_personal")
     * @throws NonUniqueResultException
     * @throws InvalidArgumentException
     */
    public function index(
        ArticleRepository $articleRepository,
        ArrayAdapter      $adapter
    ): Response {
        $user = $this->getUser();
        $expires = Carbon::parse($user->getExpireAt());
        // ToDO Почему то не сохраняется кеш
        $blocksCached = $adapter->get('app_personal_blocks', function (ItemInterface $item) use ($articleRepository, $user) {
            $item->expiresAfter(24 * 3600);
            $last = $articleRepository->findOneBy(
                [
                    'client' => $user,
                ],
                [
                    'id' => 'DESC',
                ]
            );
            // Блоки для рабочего стола
            $blocks = [
                [
                    'title' => $articleRepository->getCount($user),
                    'text' => 'Всего статей создано.',
                ],
                [
                    'title' => $articleRepository->getLastMonthCount($user),
                    'text' => 'Создано в этом месяце.',
                ],
                [
                    'title' => $user
                        ->getSubscription()
                        ->getName(),
                    'text' => 'Ваш уровень подписки.',
                    'hrefName' => 'Улучшить',
                    'hrefPath' => 'app_admin_subscription',
                ],
                [
                    'title' => 'Создать статью',
                    'hrefName' => 'Создать',
                    'hrefPath' => 'app_admin_article_create',
                ],
            ];
            // Если у пользователя есть статья (последняя созданная) то выводим блок с ней
            if (isset($last)) {
                $blocks[] = [
                    'title' => $last->getTitle(),
                    'text' => $last->getDescription() ?? '',
                    'hrefName' => 'Подробнее',
                    'hrefPath' => 'app_admin_article_show',
                    'hrefParameters' => [
                        'id' => $last->getId()
                    ],
                ];
            }

            return $blocks;
        }, 1.0);

        return $this->render(
            'admin/personal/index.html.twig',
            [
                'isExpiredSoon' => $expires->diffInDays(Carbon::now()) <= 3,
                'message' => 'Подписка истекает ' . $expires->locale('ru')->diffForHumans(),
                'blocks' => $blocksCached,
            ]
        );
    }
}
