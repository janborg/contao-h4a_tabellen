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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public const ROOT_KEY = 'janborg_h4a_tabellen';

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(self::ROOT_KEY);

        $treeBuilder->getRootNode()
            ->children()
            ->integerNode('AktuelleSpieleCacheTime')
            ->defaultValue(1800)
            ->end()
            ->integerNode('LigaSpielplanCacheTime')
            ->defaultValue(1800)
            ->end()
            ->integerNode('SpielplanCacheTime')
            ->defaultValue(1800)
            ->end()
            ->integerNode('TabellenCacheTime')
            ->defaultValue(1800)
            ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
