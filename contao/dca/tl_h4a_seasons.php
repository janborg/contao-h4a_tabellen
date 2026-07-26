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
use Janborg\H4aTabellen\HandballNet\Verband;
use Janborg\H4aTabellen\HandballNet\Provider;

$GLOBALS['TL_DCA']['tl_h4a_seasons'] = [
    // Config
    'config' => [
        'dataContainer' => DC_Table::class,
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
        'ctable' => ['tl_hn_teams'],
    ],
    'list' => [
        'sorting' => [
            'mode' => DataContainer::MODE_SORTED,
            'flag' => DataContainer::SORT_DESC,
            'fields' => ['club_id', 'season'],
            'panelLayout' => 'search, sort;filter,limit',
        ],
        'label' => [
            'fields' => ['season', 'club_name', 'verband', 'provider'],
            'format' => '%s - %s (%s, %s)',
        ],
        'global_operations' => [
            'all' => [
                'label' => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"',
            ],
            'update_hn_teams' => [
                'href' => 'key=update_hn_teams',
                'class' => 'header_update_hn_teams',
                'icon' => 'bundles/janborgh4atabellen/update.svg',
                'attributes' => 'onclick="Backend.getScrollOffset()"',
            ]
        ],
        'operations' => [
            'edit',
            'children',
            'delete',
            'show',
            'toggle' => [
				'href'                => 'act=toggle&amp;field=is_active',
				'icon'                => 'visible.svg',
				'showInHeader'        => true
			],
        ],
    ],
    // Palettes
    'palettes' => [
        'default' => '{title_legend}, season;    
                    {handballnet_legend},hn_season, club_id, club_name, provider, verband;
                    {status_legend}, is_active',
    ],
    // Fields
    'fields' => [
        'id' => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'tstamp' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'season' => [
            'label' => &$GLOBALS['TL_LANG']['tl_h4a_seasons']['season'],
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 9, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NULL default ''",
        ],
        'hn_season' => [
            'sorting' => true,
            'inputType' => 'text',
            'eval' => [
                'maxlength' => 4, 
                'tl_class' => 'w50',
                'rgxp' => 'custom',
                'customRgxp' => '/(202[1-9]|20[3-9][0-9])/',
                'errorMsg'=> 'Bitte gülitgen Wert im Format "YYYY" eingeben (2021 - heute)',
            ],
            'sql' => "varchar(4) NOT NULL default ''",
        ],
        'club_id' => [
            'inputType' => 'text',
            'eval' => [
                'mandatory' => true,
                'rgxp' => 'digit',
                'minlenght' => 1,
                'maxlength' => 4,
                'tl_class' => 'w50',
            ],
            'sql' => "varchar(4) NOT NULL default ''",
        ],
        'club_name' => [
            'inputType' => 'text',
            'sorting' => true,
            'filter' => true,
            'eval' => [
                'mandatory' => true,
                'maxlength' => 255,
                'tl_class' => 'w50',
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ], 
        'provider' => [
            'inputType' => 'select',
            'sorting' => true,
            'filter' => true,
            'enum' => Provider::class,
            'eval' => [
                'mandatory' => true,
                'maxlength' => 255,
                'tl_class' => 'w50',
                'includeBlankOption' => true,
                'chosen' => true,
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'verband' => [
            'inputType' => 'select',
            'sorting' => true,
            'filter' => true,
            'enum' => Verband::class,
            'eval' => [
                'mandatory' => true,
                'maxlength' => 255,
                'tl_class' => 'w50',
                'includeBlankOption' => true,
                'chosen' => true,
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'is_active' => [
            'toggle' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 m12'],
            'sql' => "char(1) NOT NULL default ''",
        ],
    ],
];
