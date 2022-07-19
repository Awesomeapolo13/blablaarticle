<?php

namespace App\Twig;

use Symfony\Component\Asset\Package;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Extension\RuntimeExtensionInterface;

/**
 * Расширение, переопределяющее функцию asset, для работы с файловыми системами
 */
class AppUploadedAsset implements RuntimeExtensionInterface
{

    private ParameterBagInterface $parameterBag;
    private Package $package;

    public function __construct(
        ParameterBagInterface $parameterBag,
        Package               $package
    ) {

        $this->parameterBag = $parameterBag;
        $this->package = $package;
    }

    public function asset(string $config, ?string $path): string
    {
        return $this->package->getUrl($this->parameterBag->get($config) . '/' . $path);
    }
}
