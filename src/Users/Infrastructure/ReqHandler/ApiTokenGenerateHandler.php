<?php

declare(strict_types=1);

namespace App\Users\Infrastructure\ReqHandler;

use App\Users\Domain\Dictionary\SecurityDictionary;
use App\Users\Domain\Exception\UndefinedUserException;
use App\Users\Domain\ValueObject\ApiTokenVO;
use App\Users\Infrastructure\Repository\ApiTokenRepository;
use DateTime;
use Symfony\Component\Security\Core\User\UserInterface;

class ApiTokenGenerateHandler
{
    public function __construct(private readonly ApiTokenRepository $apiTokenRepository)
    {
    }

    public function handleApiTokenGeneration(?UserInterface $user): array
    {
        if (!isset($user)) {
            throw new UndefinedUserException();
        }

        $token = $user->getApiToken();
        $token->setToken((new ApiTokenVO())->getToken());
        $token->setExpiresAt(new DateTime('+1 day'));
        $this->apiTokenRepository->save($token);

        return [
            'message' => SecurityDictionary::NEW_TOKEN_GENERATED_MSG,
            'token' => $token->getToken(),
        ];
    }
}
