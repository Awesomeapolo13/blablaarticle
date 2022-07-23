<?php

namespace App\Entity;

use App\Form\Model\ArticleFormModel;
use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * Тематика статьи
     *
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(message="Укажите тематику статьи")
     */
    private $theme;

    /**
     * Ключевые слова
     *
     * Ключевое слово и его словоформы (обязательно наличие хотя бы инфинитивной части)
     *
     * @ORM\Column(type="json")
     * @Assert\NotBlank(message="Введите ключевое слово")
     */
    private $keyWord;

    /**
     * Заголовок статьи
     *
     * @ORM\Column(type="string", length=60)
     * @Assert\NotBlank(message="Введите заголовок")
     */
    private $title;

    /**
     * Краткое описание статьи
     *
     * @ORM\Column(type="text", nullable=true)
     * @Assert\LessThanOrEqual(255)
     */
    private $description;

    /**
     * Размер статьи
     *
     * Количество модулей для генерации статьи
     *
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Укажите размер статьи")
     */
    private $size;

    /**
     * Продвигаемое в статье слово
     *
     * @ORM\Column(type="json", nullable=true)
     */
    private $promotedWords;

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

    /**
     * @ORM\OneToMany(
     *     targetEntity=ArticleImage::class,
     *     mappedBy="article",
     *     orphanRemoval=true,
     *     cascade={"persist", "remove"}
     *     )
     */
    private $images;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function setTheme(string $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    public function getKeyWord(): array
    {
        return array_unique($this->keyWord);
    }

    public function setKeyWord(array $keyWord): self
    {
        $this->keyWord = $keyWord;

        return $this;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param mixed $size
     */
    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getPromotedWords(): array
    {
        return array_unique($this->promotedWords);
    }

    public function setPromotedWords(array $promotedWords): self
    {
        $this->promotedWords = $promotedWords;

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

    /**
     * @return Collection<int, ArticleImage>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(ArticleImage $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setArticle($this);
        }

        return $this;
    }

    public function removeImage(ArticleImage $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getArticle() === $this) {
                $image->setArticle(null);
            }
        }

        return $this;
    }
}
