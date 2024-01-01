<?php

declare(strict_types=1);

namespace App\Users\Infrastructure\Service\Dashboard;

use App\Repository\ArticleRepository;
use App\Users\Domain\Dictionary\DashboardBlockDictionary;
use App\Users\Domain\Service\DashboardBlockService;
use Carbon\Carbon;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Security\Core\User\UserInterface;

class UserDashboardService
{
    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly DashboardBlockService $dashboardBlockService,
    ) {
    }

    /**
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
        $blocks = $this->dashboardBlockService->getBlocks(
            $this->articleRepository->getCount($user),
            $this->articleRepository->getLastMonthCount($user),
            $user->getSubscription()->getName(),
            $last?->getId(),
            $last?->getTitle(),
            $last?->getDescription(),
        );

        return [
            'isExpiredSoon' => $expires->diffInDays(Carbon::now()) <= DashboardBlockDictionary::IS_EXPIRES_SOON_DAYS,
            'message' => DashboardBlockDictionary::SUBSCRIPTION_EXPIRES_TEXT . $expires->locale('ru')->diffForHumans(),
            'blocks' => $blocks,
        ];
    }
}
