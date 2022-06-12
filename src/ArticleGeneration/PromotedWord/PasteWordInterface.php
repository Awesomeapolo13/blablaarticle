<?php

namespace App\ArticleGeneration\PromotedWord;

/**
 * Интерфейс для сервисов вставки слова
 */
interface PasteWordInterface
{
    /**
     * Вставляет переданное слово в переданный текст указанное количество рах
     *
     * @param string|array $text - текст куда необходимо вставить слово.
     * Может быть передан как строкой так и массивом строк
     * @param string $word - слово для вставки
     * @param int $wordCount - количество раз, которое необходимо вставить это слово
     * @param array $options - дополнительные параметры необходимые для вставки слова
     * @return string|array - возвращает исходный текст со вставленным словом
     */
    public function paste(string|array $text, string $word, int $wordCount = 1, array $options = []): string|array;
}
