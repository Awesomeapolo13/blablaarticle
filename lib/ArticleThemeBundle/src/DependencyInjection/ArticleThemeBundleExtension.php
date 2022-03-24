<?php

namespace ArticleContentProvider\ArticleThemeBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

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
    }
}