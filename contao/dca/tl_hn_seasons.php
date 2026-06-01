<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

use Contao\DC_Table;
use Contao\DataContainer;

$GLOBALS['TL_DCA']['tl_hn_seasons'] = [
    // Config
    'config' => [
        'dataContainer' => DC_Table::class,
        'ptable' => 'tl_hn_clubs',
        'ctable' => ['tl_hn_teams'],
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],
    'list' => [
        'sorting' => [
            'mode' => DataContainer::MODE_SORTED_PARENT,
            'flag' => DataContainer::SORT_BOTH,
            'fields' => ['club_name', 'season_id'],
            'panelLayout' => 'search, sort,filter,limit',
        ],
        'label' => [
            'fields' => ['name', 'season_name', 'pid'],
            'format' => '%s (%s)',
        ],
        'global_operations' => [
            'all',
            'update_hn_teams' => [
                'href' => 'key=update_hn_teams',
                'icon' => 'bundles/janborgh4atabellen/refresh.svg',
                'attributes' => 'onclick="Backend.getScrollOffset()"',
                'primary' => true
            ],
            'update_hn_clubs' => [
                'href' => 'key=update_hn_clubs',
                'icon' => 'bundles/janborgh4atabellen/refresh.svg',
                'attributes' => 'onclick="Backend.getScrollOffset()"',
                'primary' => true
            ],
            'manage_clubs' => [
                'href' => 'table=tl_hn_clubs',
                'icon' => 'bundles/janborgh4atabellen/trophy.svg',
                'primary' => true
            ],
        ],
        'operations' => [
            'edit',
            'children',
            'delete',
            'show',
            'toggle' => [
				'href'                => 'act=toggle&amp;field=is_active',
				'icon'                => 'bundles/janborgh4atabellen/refresh.svg',
                'primary'             => true,
			],
        ],
    ],
    // Palettes
    'palettes' => [
        'default' => '{title_legend}, season_name;    
                    {handballnet_legend},season_id, handballnet_club_id, club_name;
                    {status_legend}, is_active',
    ],
    // Fields
    'fields' => [
        'id' => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'pid' => [
//            'foreignKey' => 'tl_hn_clubs.name',
//            'relation' => ['type'=>'belongsTo', 'load'=>'lazy'],
            'sql' => "int(10) unsigned NOT NULL default '0'",
            'sorting' => true,
        ],
        'tstamp' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'season_name' => [
            'exclude' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 9, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NULL default ''",
        ],
        'season_id' => [
            'exclude' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => [
                'maxlength' => 4, 
                'tl_class' => 'w50',
                'rgxp' => 'custom',
                'customRgxp' => '/(202[0-9]|20[3-9][0-9])/',
                'errorMsg'=> 'Bitte gülitgen Wert im Format "YYYY" eingeben (2021 - heute)',
            ],
            'sql' => "varchar(4) NOT NULL default ''",
        ],
        'club_name' => [
            'inputType' => 'text',
            'exclude' => true,
            'sorting' => true,
            'filter' => true,
            'eval' => [
                'mandatory' => true,
                'maxlength' => 255,
                'tl_class' => 'w50',
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ], 
        'handballnet_club_id' => [
            'inputType' => 'select',
            'foreignKey' => 'tl_hn_clubs.handballnet_id',
            'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
            'eval' => [
                'mandatory' => true,
                'chosen' => true,
                'tl_class' => 'w50',
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'is_active' => [
            'toggle' => true,
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 m12'],
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
    ],
];
