<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

use Contao\DataContainer;
use Contao\DC_Table;
use Janborg\H4aTabellen\HandballNet\AgeGroup;
use Janborg\H4aTabellen\HandballNet\Provider;
use Janborg\H4aTabellen\HandballNet\Verband;

$GLOBALS['TL_DCA']['tl_hn_teams'] = [
    // Config
    'config' => [
        'dataContainer' => DC_Table::class,
        'ptable' => 'tl_hn_seasons',
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
        'backlink' => 'do=handballnet_teams'
    ],
    'list' => [
        'sorting' => [
            'mode' => DataContainer::MODE_PARENT,
            'flag' => DataContainer::SORT_INITIAL_LETTERS_BOTH,
            'headerFields' => ['club_name', 'season_name', 'handballnet_club_id'],
            'fields' => ['verband'],
            'panelLayout' => 'search;filter;limit',
        ],
        'label' => [
            'fields' => ['age_group', 'liga_name', 'handballnet_team_id'],
            'format' => '%s – %s [%s]',
        ],
        'global_operations' => [
            'all' => [
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"',
            ],
        ],
        'operations' => [
            'edit',
            'delete',
            'toggle' => [
                'href'    => 'act=toggle&amp;field=is_active',
                'icon'    => 'bundles/janborgh4atabellen/refresh.svg',
                'primary' => true,
            ],
            'show',
        ],
    ],
    // Palettes
    'palettes' => [
        'default' => '{title_legend},saison,provider,verband;
                      {handballnet_team_legend},my_team_name,team_group_id,handballnet_team_id,team_logo;
                      {handballnet_tournament_legend},liga_name,liga_shortname,handballnet_tournament_id,tournament_type,age_group;
                      {status_legend},is_active',
    ],
    // Fields
    'fields' => [
        'id' => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'pid' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'tstamp' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'saison' => [
            'inputType' => 'text',
            'exclude'   => true,
            'sorting'   => true,
            'eval' => [
                'mandatory' => true,
                'rgxp'      => 'digit',
                'maxlength' => 4,
                'tl_class'  => 'w50',
                'readonly'  => true,
            ],
            'sql' => "varchar(4) NOT NULL default '0'",
        ],
        'provider' => [
            'inputType' => 'select',
            'exclude'   => true,
            'sorting'   => true,
            'filter'    => true,
            'enum'      => Provider::class,
            'eval' => [
                'mandatory'          => true,
                'maxlength'          => 255,
                'tl_class'           => 'w50 clr',
                'includeBlankOption' => true,
                'chosen'             => true,
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'verband' => [
            'inputType' => 'select',
            'exclude'   => true,
            'sorting'   => true,
            'filter'    => true,
            'enum'      => Verband::class,
            'eval' => [
                'mandatory'          => true,
                'maxlength'          => 255,
                'tl_class'           => 'w50',
                'includeBlankOption' => true,
                'chosen'             => true,
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],

        // === Team-Daten aus handball.net API ===

        'handballnet_team_id' => [
            'inputType' => 'text',
            'exclude'   => true,
            'search'    => true,
            'eval' => [
                'maxlength' => 255,
                'tl_class'  => 'w50 clr',
                'readonly'  => true,
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'team_logo' => [
            // API: data[].logo
            'inputType' => 'text',
            'exclude'   => true,
            'eval' => [
                'maxlength' => 500,
                'tl_class'  => 'w50',
                'readonly'  => true,
            ],
            'sql' => "varchar(500) NOT NULL default ''",
        ],
        'team_group_id' => [
            // API: data[].teamGroupId
            'inputType' => 'text',
            'exclude'   => true,
            'eval' => [
                'rgxp'     => 'digit',
                'maxlength' => 20,
                'tl_class'  => 'w50',
                'readonly'  => true,
            ],
            'sql' => "varchar(20) NOT NULL default ''",
        ],

        // === Tournament-Daten aus handball.net API ===

        'handballnet_tournament_id' => [
            // API: data[].defaultTournament.id
            'inputType' => 'text',
            'exclude'   => true,
            'search'    => true,
            'eval' => [
                'maxlength' => 255,
                'tl_class'  => 'w50 clr',
                'readonly'  => true,
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'liga_name' => [
            // API: data[].defaultTournament.name
            'inputType' => 'text',
            'exclude'   => true,
            'sorting'   => true,
            'filter'    => true,
            'search'    => true,
            'eval' => [
                'maxlength' => 255,
                'tl_class'  => 'w50',
                'readonly'  => true,
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'liga_shortname' => [
            // API: data[].defaultTournament.acronym
            'inputType' => 'text',
            'exclude'   => true,
            'sorting'   => true,
            'filter'    => true,
            'search'    => true,
            'eval' => [
                'maxlength' => 60,
                'tl_class'  => 'w50',
                'readonly'  => true,
            ],
            'sql' => "varchar(60) NOT NULL default ''",
        ],
        'tournament_type' => [
            // API: data[].defaultTournament.tournamentType  z.B. "League"
            'inputType' => 'text',
            'exclude'   => true,
            'filter'    => true,
            'eval' => [
                'maxlength' => 50,
                'tl_class'  => 'w50',
                'readonly'  => true,
            ],
            'sql' => "varchar(50) NOT NULL default ''",
        ],
        'age_group' => [
            // API: data[].defaultTournament.ageGroup  z.B. "Men", "Women", "AYouth" ...
            'inputType' => 'select',
            'exclude'   => true,
            'sorting'   => true,
            'filter'    => true,
            'enum'      => AgeGroup::class,
            'eval' => [
                'maxlength' => 50,
                'tl_class'  => 'w50',
                'includeBlankOption' => true,
                'chosen'             => true,
            ],
            'sql' => "varchar(50) NOT NULL default ''",
        ],

        // === Eigene Felder ===

        'my_team_name' => [
            'inputType' => 'text',
            'exclude'   => true,
            'sorting'   => true,
            'filter'    => true,
            'eval' => [
                'mandatory' => true,
                'maxlength' => 255,
                'tl_class'  => 'w50',
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'is_active' => [
            'toggle'    => true,
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50 m12'],
            'sql'       => ['type' => 'boolean', 'default' => false],
        ],
    ],
];