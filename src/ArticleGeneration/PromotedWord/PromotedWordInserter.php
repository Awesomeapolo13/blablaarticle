<?php

namespace App\ArticleGeneration\PromotedWord;

/**
 *  Класс вставки продвигаемых слов
 */
class PromotedWordInserter implements PasteWordInterface
{
    /**
     * Вставляет переданное продвигаемое слово в текст указанное количество раз
     *
     * @param string|array $text
     * @param string $word
     * @param int $wordCount
     * @return string|array
     */
    public function paste(string|array $text, string $word, int $wordCount = 1): string|array
    {
        for ($i = 1; $i <= $wordCount; $i++) {
            // Выбираем случайный модуль через случайную позицию
            $randModulePos = rand(0, count($text) - 1);
            // Выбираем текст из случайного модуля, он будет в массиве под ключом 1 $matches
            if (
                preg_match_all(
                    '/(?:<\w+\d?(?:.*)?>)(.+)?(?:<\/\w+\d?>)/',
                    $text[$randModulePos],
                    $matches
                )
            ) {
                // Выбираем случайный текст в модуле
                $targetText = $matches[1][rand(0, count($matches[1]) - 1)];
                // Разбиваем его на слова
                $textArr = explode(' ', $targetText);
                // Вставляем продвигаемое слово
                array_splice($textArr, rand(0, count($textArr) - 2), 0, $word);
                // Обрезаем выбранный случайный текст для использования его в preg_replace
                $targetText = mb_substr($targetText, 0, 30);
                // Заменяет исходный текст на тот, что был дополнен продвигаемым словом
                // ToDO  Вероятно вставит текст до первого закрывающего тега, что не правильно. Придумать как этого избежать
                $text[$randModulePos] = preg_replace(
                    "/($targetText.*?)<\/\w+\d?>/",
                    implode(' ', $textArr),
                    $text[$randModulePos]
                );
            }
        }

        return $text;
    }
}
