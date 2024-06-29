<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

namespace Janborg\H4aTabellen\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class JanborgH4aTabellenExtension extends Extension
{
    public function getAlias(): string
    {
        return Configuration::ROOT_KEY;
    }

    /**
     * Loads configuration.
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $fileLocator = new FileLocator(__DIR__.'/../../config');
        $loader = new YamlFileLoader($container, $fileLocator);
        $configuration = new Configuration();

        $loader->load('commands.yml');
        $loader->load('services.yml');
        $loader->load('migrations.yml');
        $loader->load('listener.yml');

        $rootKey = $this->getAlias();
        $config = $this->processConfiguration($configuration, $configs);

        // Configuration
        $container->setParameter($rootKey.'.AktuelleSpieleCacheTime', $config['AktuelleSpieleCacheTime']);
        $container->setParameter($rootKey.'.LigaSpielplanCacheTime', $config['LigaSpielplanCacheTime']);
        $container->setParameter($rootKey.'.SpielplanCacheTime', $config['SpielplanCacheTime']);
        $container->setParameter($rootKey.'.TabellenCacheTime', $config['TabellenCacheTime']);
    }
}
