<?php

namespace App\Command;

use App\Entity\Subscription;
use App\Entity\User;
use App\Repository\SubscriptionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Команда, сбрасывающая подписку до уровня free, если пользователь вовремя ее не продлил
 */
class DowngradeExpiredSubscriptionCommand extends Command
{
    protected static $defaultName = 'app:subscription:downgrade';
    protected static $defaultDescription = 'Команда, сбрасывающая подписку до уровня free, если пользователь вовремя ее не продлил';

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(
        UserRepository $userRepository,
        SubscriptionRepository $subscriptionRepository,
        EntityManagerInterface $em
    )
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->em = $em;
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
            $this->em->persist($user);
        }

        $this->em->flush();

        $io->success('All user subscriptions updated');

        return Command::SUCCESS;
    }
}
