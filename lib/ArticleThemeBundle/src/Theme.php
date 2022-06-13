<?php

namespace ArticleThemeProvider\ArticleThemeBundle;

/**
 * Класс тематики
 */
class Theme
{
    private string $slug;
    private string $name;
    private array $paragraphs;

    public function __construct(string $slug, string $name, array $paragraphs)
    {
        $this->slug = $slug;
        $this->name = $name;
        $this->paragraphs = $paragraphs;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getParagraphs(): array
    {
        return $this->paragraphs;
    }
}
