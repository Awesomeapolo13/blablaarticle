<?php

namespace ArticleThemeProvider\ArticleThemeBundle\Provider;

use ArticleThemeProvider\ArticleThemeBundle\Theme;
use ArticleThemeProvider\ArticleThemeBundle\ThemeProviderInterface;

/**
 * Провайдер тем для демонстрационной генерации статей
 */
class DemoThemeProvider implements ThemeProviderInterface
{
    /**
     * Коллекция тем для демонстрационной генерации
     *
     * @return array
     */
    public function getThemes(): array
    {
        return [
            new Theme('demo', 'Для демонстрации', [
                'Вот что умеет наш умный генератор статей, а ведь это только демонстрация!'
            ]),
        ];
    }
}
