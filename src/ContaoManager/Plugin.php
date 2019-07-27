<?php

declare(strict_types=1);

namespace Loilo\ContaoIlluminateDatabaseBundle\ContaoManager;

use Loilo\ContaoIlluminateDatabaseBundle\LoiloContaoIlluminateDatabaseBundle;
use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;

class Plugin implements BundlePluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $_parser): array
    {
        return [
            BundleConfig::create(LoiloContaoIlluminateDatabaseBundle::class)
                ->setLoadAfter([ ContaoCoreBundle::class ])
        ];
    }
}
