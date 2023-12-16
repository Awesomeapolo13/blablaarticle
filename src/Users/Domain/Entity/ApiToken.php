<?php

declare(strict_types=1);

namespace App\Users\Domain\Entity;

use DateTime;
use DateTimeInterface;
use Exception;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Security\Core\User\UserInterface;

class ApiToken
{
    use TimestampableEntity;

    private int $id;
    private string $token;
    private DateTimeInterface $expiresAt;
    private User $client;
    protected $createdAt;
    protected $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getExpiresAt(): ?DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(DateTimeInterface $expiresAt): self
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(User $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     *  Фабричный метод для создания объекта api-токена
     *
     * @throws Exception
     */
    public static function create(UserInterface $user, string $dateTime = '+1 day'): ApiToken
    {
        return (new self())
            ->setToken(sha1(uniqid('token', true)))
            ->setClient($user)
            ->setExpiresAt(new DateTime($dateTime));
    }

    /**
     * Проверяет время жизни api токена
     *
     * @return bool - возвращает true, если текущая дата и время меньше черм время жизни токена
     */
    public function isExpired(): bool
    {
        return $this->getExpiresAt() <= new DateTime();
    }
}
