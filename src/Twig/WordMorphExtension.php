<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Расширение для словоформ ключевых слов
 */
class WordMorphExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('morph', [$this, 'getMorph']),
        ];
    }

    public function getMorph(array $keyWord, int $morphNumber)
    {
        return $keyWord[$morphNumber] ?: $keyWord[0];
    }
}
