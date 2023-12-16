<?php

declare(strict_types=1);

namespace App\Users\Domain\Entity;

use App\Entity\Article;
use App\Entity\Module;
use App\Entity\Subscription;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class User implements UserInterface
{
    use TimestampableEntity;

    private int $id;
    /**
     * @Groups("api_user")
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private string $email;
    private array $roles = [];
    /**
     * @Assert\NotBlank(message="Введите пароль")
     * @Assert\Length(min="6", minMessage="Минимальная длина пароля 6 символов")
     */
    private string $password;
    /**
     * @Groups("api_user")
     * @Assert\NotBlank(message="Заполните имя")
     * @Assert\Length(min="2", minMessage="Минимальная длинна имени 2 символа")
     */
    private string $firstName;
    private bool $isEmailConfirmed;
    /**
     * Дата истечения подписки
     */
    private DateTimeInterface $expireAt;
    protected $createdAt;
    protected $updatedAt;
    private ?Subscription $subscription;
    private ApiToken $apiToken;
    private Collection $modules;
    private Collection $articles;

    public function __construct()
    {
        $this->modules = new ArrayCollection();
        $this->articles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getIsEmailConfirmed(): ?bool
    {
        return $this->isEmailConfirmed;
    }

    public function setIsEmailConfirmed(bool $isEmailConfirmed): self
    {
        $this->isEmailConfirmed = $isEmailConfirmed;

        return $this;
    }

    public function getSubscription(): ?Subscription
    {
        return $this->subscription;
    }

    public function setSubscription(?Subscription $subscription): self
    {
        $this->subscription = $subscription;

        return $this;
    }

    public function getExpireAt(): DateTimeInterface
    {
        return $this->expireAt;
    }

    public function setExpireAt(DateTimeInterface $expireAt): self
    {
        $this->expireAt = $expireAt;

        return $this;
    }

    public function getApiToken(): ?ApiToken
    {
        return $this->apiToken;
    }

    public function setApiToken(ApiToken $apiToken): self
    {
        if ($apiToken->getClient() !== $this) {
            $apiToken->setClient($this);
        }

        $this->apiToken = $apiToken;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getModules(): Collection
    {
        return $this->modules;
    }

    public function addModule(Module $module): self
    {
        if (!$this->modules->contains($module)) {
            $this->modules[] = $module;
            $module->setClient($this);
        }

        return $this;
    }

    public function removeModule(Module $module): self
    {
        if ($this->modules->removeElement($module)) {
            if ($module->getClient() === $this) {
                $module->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->setClient($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getClient() === $this) {
                $article->setClient(null);
            }
        }

        return $this;
    }
}
