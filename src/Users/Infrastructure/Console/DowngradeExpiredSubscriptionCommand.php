<?php

namespace App\Users\Infrastructure\Console;

use App\Entity\Subscription;
use App\Repository\SubscriptionRepository;
use App\Users\Domain\Entity\User;
use App\Users\Infrastructure\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Команда, сбрасывающая подписку до уровня free, если пользователь вовремя ее не продлил
 */
final class DowngradeExpiredSubscriptionCommand extends Command
{
    protected static $defaultName = 'app:subscription:downgrade';
    protected static string $defaultDescription = 'Команда, сбрасывающая подписку до уровня free, если пользователь вовремя ее не продлил';

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly SubscriptionRepository $subscriptionRepository,
        private readonly EntityManagerInterface $em
    )
    {
        parent::__construct();
    }


    protected function configure(): void
    {
        $this->setDescription(self::$defaultDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->info('Try to get users with expired subscription');

        // ToDO: быть может нужно найти первые 100 или 1000 пользователей и тогда только их обновлять. Не знаю как лучше.
        /** @var User[] $users */
        $users = $this->userRepository->findAllExpiredUsers();

        /** @var Subscription $freeSubscription */
        $freeSubscription = $this->subscriptionRepository->findOneBy(['name' => 'FREE']);

        if (empty($users)) {
            $io->success('Didn`t find any user with expired subscription. Command is out');
            return Command::SUCCESS;
        }

        if (empty($freeSubscription)) {
            $io->success('Didn`t find such subscription as free');
            return Command::FAILURE;
        }

        foreach ($users as $user) {
            $user->setSubscription($freeSubscription);
        }

        $this->em->flush();

        $io->success('All user subscriptions updated');

        return Command::SUCCESS;
    }
}
