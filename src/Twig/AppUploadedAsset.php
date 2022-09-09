<?php

namespace App\Twig;

use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Extension\RuntimeExtensionInterface;

/**
 * Расширение, переопределяющее функцию asset, для работы с файловыми системами
 */
class AppUploadedAsset implements RuntimeExtensionInterface
{

    private ParameterBagInterface $parameterBag;
    private Packages $packages;

    public function __construct(
        ParameterBagInterface $parameterBag,
        Packages               $packages
    ) {

        $this->parameterBag = $parameterBag;
        $this->packages = $packages;
    }

    /**
     * Возвращает строку пути к файлу
     *
     * @param string $config - основной путь до директории хранения файла, взятый из конфига
     * @param string|null $path - имя файла или оставшийся до него путь из директории
     * @return string
     */
    public function asset(string $config, ?string $path): string
    {
        $prefix = $config === 'article_uploads_img_url' ? '' : $this->parameterBag->get($config) . '/';
        return $this->packages->getUrl($prefix . $path);
    }
}
