<?php

namespace App\ArticleGeneration\Strategy;

/**
 * Генерация статьи для пользователя с подпиской FREE
 */
class FreeGenerationStrategy extends BaseStrategy
{
    /**
     * Для этой стратегии возможна генерация с применением только базовой формы ключевого слова
     * @param array $keyWords
     * @return array
     */
    protected function resolveKeyWord(array $keyWords): array
    {
        return [$keyWords[0]];
    }
}
