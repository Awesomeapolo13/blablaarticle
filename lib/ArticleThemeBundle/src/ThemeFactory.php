<?php

namespace ArticleThemeProvider\ArticleThemeBundle;

use ArticleThemeProvider\ArticleThemeBundle\ThemeProviderInterface;
use ArticleThemeProvider\ArticleThemeBundle\ThemeStrategyInterface;

/**
 * Класс для создания тем
 *
 * Создает темы в соответствии с переданными стратегиями
 */
class ThemeFactory
{
    /**
     * Все провайдеры тематик
     *
     * @var iterable|ThemeProviderInterface[]
     */
    private $allThemeProviders;

    public function __construct(
        iterable $allThemeProviders
    )
    {
        $this->allThemeProviders = $allThemeProviders;
    }

    /**
     * Получает все доступные темы
     *
     * @return Theme[]
     */
    public function getThemes(): array
    {
        $themes = [];
        foreach ($this->allThemeProviders->getIterator() as $provider) {
            $themes = array_merge($themes, $provider->getThemes());
        }

        return array_unique($themes, SORT_REGULAR);
    }

    /**
     * Ищет тематику по slug
     *
     * @param string $slug - уникальный символьный код тематики
     * @return Theme|null - возвращает конкретную тематику, если находит ее иначе null
     */
    public function findThemeBySlug(string $slug): ?Theme
    {
        $themes = $this->getThemes();

        foreach ($themes as $theme) {
            if ($theme->getSlug() === $slug) {
                return $theme;
            }
        }
    }
}
