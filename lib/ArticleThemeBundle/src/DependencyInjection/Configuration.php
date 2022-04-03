<?php

namespace ArticleThemeProvider\ArticleThemeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Конфигурация бандла
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Задает дерево кинфигураций
     *
     * @return TreeBuilder
     */

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('article_theme_bundle');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
            ->arrayNode('theme_providers_collection')
            ->defaultValue(['ArticleThemeProvider\\ArticleThemeBundle\\Provider\\BasicThemeProvider'])
            ->scalarPrototype()
            ->end()
            ->info('Theme providers collection')
            ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
