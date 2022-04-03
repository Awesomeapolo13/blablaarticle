<?php

namespace ArticleThemeProvider\ArticleThemeBundle\Provider;

use ArticleThemeProvider\ArticleThemeBundle\Theme;
use ArticleThemeProvider\ArticleThemeBundle\ThemeProviderInterface;

/**
 * Провайдер получения обыденных базовыъ тем
 */
class BasicThemeProvider implements ThemeProviderInterface
{
    /**
     * Коллекция тем для демонстрационной генерации
     *
     * @return array
     */
    public function getThemes(): array
    {
        return [
            new Theme('common_whether', 'О текущей погоде', [
                'Погода в данный момент вещь не стабильная, она как будто {{ keyword }}!',
                'Помнитася прошлым летом погода была ничем не примещательна.'
            ]),
            new Theme('common_auto', 'О нынешних машинах', [
                'Среди нынешних машин выделяются наша. У меня ее никогда и небыло.',
            ]),
        ];
    }
}
