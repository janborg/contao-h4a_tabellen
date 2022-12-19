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
    /**
     * Loads configuration.
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $fileLocator = new FileLocator(__DIR__.'/../../config');
        $loader = new YamlFileLoader($container, $fileLocator);

        $loader->load('commands.yml');
        $loader->load('services.yml');
        $loader->load('migrations.yml');
        $loader->load('listener.yml');
    }
}
