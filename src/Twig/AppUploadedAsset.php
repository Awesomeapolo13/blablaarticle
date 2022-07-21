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

    public function asset(string $config, ?string $path): string
    {
        return $this->packages->getUrl($this->parameterBag->get($config) . '/' . $path);
    }
}
