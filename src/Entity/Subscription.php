<?php

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use App\Users\Domain\Entity\User;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=SubscriptionRepository::class)
 */
class Subscription
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
    private $name;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $price;

    /**
     * @ORM\Column(type="array")
     */
    private $opportunities = [];

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="subscription")
     */
    private $users;

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
     * Время в минутах, на которое блокируется генерация статьи для определенного типа подписки
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $blockTime;

    /**
     * Количество статей, которые можно сгенерировать после истечения $blockTime
     * @ORM\Column(type="integer", length=11, nullable=true)
     */
    private $blockCount;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

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

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getOpportunities(): ?array
    {
        return $this->opportunities;
    }

    public function setOpportunities(array $opportunities): self
    {
        $this->opportunities = $opportunities;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setSubscription($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getSubscription() === $this) {
                $user->setSubscription(null);
            }
        }

        return $this;
    }

    public function getBlockTime(): ?string
    {
        return $this->blockTime;
    }

    public function setBlockTime(?string $blockTime): self
    {
        $this->blockTime = $blockTime;

        return $this;
    }

    public function getBlockCount(): ?int
    {
        return $this->blockCount;
    }

    public function setBlockCount(?int $blockCount): self
    {
        $this->blockCount = $blockCount;

        return $this;
    }
}
