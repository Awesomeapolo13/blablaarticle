<?php

namespace ArticleThemeProvider\ArticleThemeBundle;

/**
 * Интерфейс для классов провайдеров тематик
 */
interface ThemeProviderInterface
{
    /**
     * Получает массив классов тематик
     *
     * @return array
     */
    public function getThemes(): array;
}
