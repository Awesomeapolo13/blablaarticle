<?php

namespace ArticleContentProvider\ArticleThemeBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use ArticleContentProvider\ArticleThemeBundle\DependencyInjection\ArticleThemeBundleExtension;

/**
 * Бандл для тематик статей
 */
class ArticleThemeBundle extends Bundle
{
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new ArticleThemeBundleExtension();
        }

        return $this->extension;
    }
}
