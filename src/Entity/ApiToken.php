<?php

namespace App\Entity;

use App\Repository\ApiTokenRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=ApiTokenRepository::class)
 */
class ApiToken
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $token;

    /**
     * @ORM\Column(type="datetime")
     */
    private $expiresAt;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="apiToken", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
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

    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(\DateTimeInterface $expiresAt): self
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
     * @param UserInterface $user
     * @param string $dateTime - метка времени для передачи в объект \DateTime
     * @return ApiToken
     * @throws \Exception
     */
    public static function create(UserInterface $user, string $dateTime = '+1 day'): ApiToken
    {
        return (new self())
            ->setToken(sha1(uniqid('token', true)))
            ->setClient($user)
            ->setExpiresAt(new \DateTime($dateTime))
            ;
    }

    /**
     * Проверяет время жизни api токена
     *
     * @return bool - возвращает true, если текущая дата и время меньше черм время жизни токена
     */
    public function isExpired(): bool
    {
        return $this->getExpiresAt() <= new \DateTime();
    }
}
