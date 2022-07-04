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
     * @ORM\OneToMany(targetEntity=ArticleImage::class, mappedBy="article", orphanRemoval=true)
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
     * Фабричный метод создания статьи
     * ToDo Вынести фабричные методы в отдельный класс
     *
     * Создает объект статьи из объекта DTO формы генерации статьи.
     * Задает все необходимые свойства кроме body. Его нужно получить во время генерации и задать отдельно.
     *
     * @param ArticleFormModel $articleFormModel - DTO формы генерации статьи
     * @return Article
     */
    public static function create(ArticleFormModel $articleFormModel): Article
    {
        $article = new self();
        // сохраняем описание, если оно есть
        if (isset($articleFormModel->description)) {
            $article->setDescription($articleFormModel->description);
        }
        // Формируем массив продвигаемых слов
        if (!empty($articleFormModel->promotedWords)) {
            $promotedWords = [];
            foreach ($articleFormModel->promotedWords as $key => $promotedWord) {
                $promotedWords[] = [
                    'word' => $promotedWord,
                    'count' => $articleFormModel->promotedWordCount[$key],
                ];
            }
            $article->setPromotedWords($promotedWords);
        }
        // Формируем заголовок из slug тематики, если он не задан (ToDo У тематики есть name, надо как то использовать его)
        if (!isset($articleFormModel->title)) {
            $title = explode('_',$articleFormModel->theme);
            // Заголовок должен начинаться с заглавной буквы
            $title[0] = ucfirst($title[0]);
            $articleFormModel->title = implode(' ', $title);
        }
        // Если определен один из параметров, то выбираем то что определен
        $size = $articleFormModel->sizeFrom ?? null;
        if (isset($articleFormModel->sizeTo)) {
            $size = $articleFormModel->sizeTo;
        }
        // Если определены оба, то выбираем рандомное количество модулей между полученными значений
        if (isset($articleFormModel->sizeFrom) && isset($articleFormModel->sizeTo)) {
            $size = rand($articleFormModel->sizeFrom, $articleFormModel->sizeTo);
        }

        return $article
                ->setTheme($articleFormModel->theme)
                ->setKeyWord($articleFormModel->articleWords)
                ->setTitle($articleFormModel->title)
                ->setSize($size)
            ;
    }

    /**
     * Создает объект статьи для демонстрационной генерации
     *
     * @param string $title - заголовок статьи
     * @param string $body - тело сгенерированной статьи
     * @param string $promotedWord - продвигаемое слово
     * @return Article
     */
    public static function createDemo(
        string $title,
        string $body,
        string $promotedWord
    ): Article
    {
        return (new self())
            ->setTheme('demo')
            ->setKeyWord(['demonstration'])
            ->setTitle($title)
            ->setSize(3)
            ->setPromotedWords(['word' => $promotedWord, 'count' => 1])
            ->setBody($body)
            ;
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
