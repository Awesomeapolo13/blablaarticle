<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Сущность сгенерированной статьи
 *
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 */
class Article
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Заголовок статьи
     *
     * @ORM\Column(type="string", length=60)
     * @Assert\NotBlank(message="Введите заголовок")
     */
    private $title;

    /**
     * Продвигаемое в статье слово
     *
     * @ORM\Column(type="string", length=60)
     * @Assert\NotBlank(message="Введите продвигаемое слово")
     */
    private $promotedWord;

    /**
     * Тело статьи
     *
     * Содержит html разметку и текст сгенерированной статьи
     *
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Отсутствует результат генерации статьи")
     */
    private $body;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     * @Assert\DisableAutoMapping()
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     * @Assert\DisableAutoMapping()
     */
    protected $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPromotedWord(): ?string
    {
        return $this->promotedWord;
    }

    public function setPromotedWord(string $promotedWord): self
    {
        $this->promotedWord = $promotedWord;

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
}
