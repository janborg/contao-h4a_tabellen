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

        /** @phpstan-ignore-next-line */
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
            ->integerNode('h4aCacheTtl')
            ->defaultValue(3600)
            ->end()
            ->integerNode('handballnet_cache_ttl')
            ->defaultValue(900)
            ->end()
            ->scalarNode('current_season')
            ->defaultValue('2025')
            ->validate()
            ->ifTrue(static fn (string $v) => !preg_match('/^20\d{2}$/', $v))
            ->thenInvalid('aktuelle_saison muss 4 Ziffern haben, z. B. "2025"')
            ->end()
            ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
