<?php

declare(strict_types=1);

namespace App\Users\Infrastructure\Service\Dashboard;

use App\Repository\ArticleRepository;
use App\Users\Domain\Entity\User;
use Carbon\Carbon;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Security\Core\User\UserInterface;

class UserDashboardService
{
    public function __construct(
        private readonly ArticleRepository $articleRepository
    ) {
    }

    /**
     * @param User $user
     *
     * @throws NonUniqueResultException
     */
    public function getDashboard(UserInterface $user): array
    {
        $expires = Carbon::parse($user->getExpireAt());

        $last = $this->articleRepository->findOneBy(
            [
                'client' => $user,
            ],
            [
                'id' => 'DESC',
            ]
        );
        // Блоки для рабочего стола
        // ToDo: Формирование блоков вынести в отдельный сервис.
        $blocks = [
            [
                'title' => $this->articleRepository->getCount($user),
                'text' => 'Всего статей создано.',
            ],
            [
                'title' => $this->articleRepository->getLastMonthCount($user),
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

        return [
            'isExpiredSoon' => $expires->diffInDays(Carbon::now()) <= 3,
            'message' => 'Подписка истекает ' . $expires->locale('ru')->diffForHumans(),
            'blocks' => $blocks,
        ];
    }
}
