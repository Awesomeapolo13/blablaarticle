<?php

namespace ArticleThemeProvider\ArticleThemeBundle\DependencyInjection;

use ArticleThemeProvider\ArticleThemeBundle\ThemeProviderInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Класс расширения
 */
class ArticleThemeBundleExtension extends Extension
{
    /**
     * Загружает конфигурации и расширение бандла
     *
     * @param array $configs
     * @param ContainerBuilder $container
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        // Задаем путь до файла с конфигурацией сервиса
        $loader = new XmlFileLoader($container, new FileLocator(dirname(__DIR__) . '/Resources/config'));
        // Загружаем конфигурацию сервиса
        $loader->load('services.xml');

        // добавляем кастомный тег
//        $container->registerForAutoconfiguration(ThemeProviderInterface::class)
//            ->addTag('article_theme_provider.theme_provider');
//
//        $configuration = $this->getConfiguration($configs, $container);
//        $config = $this->processConfiguration($configuration, $configs);
//        $definition = $container->getDefinition('article_theme_provider.theme_factory');
    }

    public function getAlias(): string
    {
        return 'article_theme_bundle';
    }
}
