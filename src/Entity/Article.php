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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $images;

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

    public function getImages(): ?string
    {
        return $this->images;
    }

    public function setImages(?string $images): self
    {
        $this->images = $images;

        return $this;
    }

    /**
     * Фабричный метод создания статьи
     *
     * @param string $theme - тематика
     * @param array $keyWord - массив ключевого слова и его словоформ
     * @param string $title - заголовок
     * @param int $size - размер
     * @param array $promotedWords - продвигаемое слово
     * @param string $body - тело статьи
     * @param string|null $description - краткое описание статьи
     * @return Article
     */
    public static function create(
        string $theme,
        array $keyWord,
        string $title,
        int $size,
        string $body,
        array $promotedWords = [],
        string $description = null
    ): Article
    {
        $article = new self();
        if (isset($description)) {
            $article->setDescription($description);
        }

        if (!empty($promotedWords)) {
            $article->setPromotedWords($promotedWords);
        }

        return $article
                ->setTheme($theme)
                ->setKeyWord($keyWord)
                ->setTitle($title)
                ->setSize($size)
                ->setBody($body)
            ;
    }
}
