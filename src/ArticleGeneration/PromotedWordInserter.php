<?php

namespace App\ArticleGeneration;

use Exception;

/**
 *  Класс вставки продвигаемых слов
 */
class PromotedWordInserter
{
    /**
     * Вставляет продвигаемое слово в переданный текст указанное количество раз
     *
     * @param string $promotedWord - продвигаемое слово
     * @param string $text - текст куда следует вставить продвигаемое слово
     * @param int $wordsCount - количество вставок продвигаемого слова
     * @return string               - исходный текст с заданным количеством вставок продвигаемого слова
     * @throws Exception
     */
    public function pasteWordIntoText(string $promotedWord, string $text, int $wordsCount = 1): string
    {
        $textArray = explode(' ', $text);
        for ($i = 1; $i <= $wordsCount; $i++) {
            array_splice($textArray, random_int(1, count($textArray) - 2), 0, $promotedWord);
        }

        return implode(' ', $textArray);
    }

    /**
     * Вставляет продвигаемое слово в каждый параграф указанное количество раз
     *
     * @param string $promotedWord - продвигаемое слово
     * @param array $paragraphs - параграфы, в текст которых следует вставить продвигаемое слово
     * @param int $wordsCount - количество вставок продвигаемого слова
     * @return array                - массив параграфов, в каждом из которых заданное количество раз
     *                                вставлено продвигаемое слово
     * @throws Exception
     */
    public function pasteWordIntoParagraphs(string $promotedWord, array $paragraphs, int $wordsCount = 1): array
    {
        foreach ($paragraphs as $key => $paragraph) {
            $paragraphs[$key] = $this->pasteWordIntoText($promotedWord, $paragraph, $wordsCount);
        }

        return $paragraphs;
    }
}
