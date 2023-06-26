<?php

namespace App\Entity;

use App\Repository\ModuleRepository;
use App\Users\Domain\Entity\User;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Сущность модуля для генерации статей
 *
 * @ORM\Entity(repositoryClass=ModuleRepository::class)
 */
class Module
{
    use TimestampableEntity;
    use SoftDeleteableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Название модуля
     *
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * Код модуля
     *
     * @ORM\Column(type="text")
     */
    private $body;

    /**
     * Пользователь - владелец этого модуля
     *
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="modules")
     */
    private $client;

    /**
     * @var DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @var DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    protected $updatedAt;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    protected $deletedAt;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isDefault;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getClient(): ?UserInterface
    {
        return $this->client;
    }

    public function setClient(?UserInterface $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getIsDefault(): ?bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(?bool $isDefault): self
    {
        $this->isDefault = $isDefault;

        return $this;
    }
}

