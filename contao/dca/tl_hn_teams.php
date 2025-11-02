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

$GLOBALS['TL_DCA']['tl_hn_teams'] = [
    // Config
    'config' => [
        'dataContainer' => DC_Table::class,
        'ptable' => 'tl_h4a_seasons',
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],
    'list' => [
        'sorting' => [
            'mode' => DataContainer::MODE_PARENT,
            'flag' => DataContainer::SORT_INITIAL_LETTER_DESC,
            'headerFields' => ['hn_season', 'club_name', 'club_id'],
            'fields' => ['liga_shortname'],
            'panelLayout' => 'search;filter;limit',
        ],
        'label' => [
            'fields' => ['liga_shortname', 'team_id', 'liga_name'],
            'format' => '%s (%s) | %s',
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
				'href'                => 'act=toggle&amp;field=is_active',
				'icon'                => 'visible.svg',
				'showInHeader'        => true
			],
            'show',
        ],
    ],
    // Palettes
    'palettes' => [
        'default' => '{title_legend},saison,provider,verband;{handballnet_tournament_legend},liga_shortname,liga_name,handballnet_tournament_id;{handballnet_team_legend},team_id,my_team_name,handballnet_team_id; {status_legend}, is_active',
    ],
    // Fields
    'fields' => [
        'id' => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'pid' => array(
            'sql' => "int(10) unsigned NOT NULL default '0"
        ),
        'tstamp' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'saison' => [
            'inputType' => 'text',
            'exclude' => true,
            'sorting' => true,
            'filter' => false,
            'eval' => [
                'mandatory' => true,
                'rgxp' => 'digit',
                'maxlength' => 4,
                'tl_class' => 'w50',
                'readonly' => true,
            ],
            'sql' => "varchar(4) unsigned NOT NULL default '0'",
        ],
        'team_id' => [
            'inputType' => 'text',
            'exclude' => true,
            'sorting' => true,
            'search' => true,
            'eval' => [
                'mandatory' => true,
                'rgxp' => 'digit',
                'maxlength' => 7,
                'tl_class' => 'w50',
            ],
            'sql' => "varchar(10) unsigned NOT NULL default ''",
        ],
        'liga_shortname' => [
            'inputType' => 'text',
            'exclude' => true,
            'sorting' => true,
            'filter' => true,
            'search' => true,
            'eval' => [
                'mandatory' => true,
                'maxlength' => 20,
                'tl_class' => 'w50',
            ],
            'sql' => "varchar(20) NOT NULL default ''",
        ],
        'liga_name' => [
            'inputType' => 'text',
            'exclude' => true,
            'sorting' => true,
            'filter' => true,
            'search' => true,
            'eval' => [
                'mandatory' => true,
                'maxlength' => 255,
                'tl_class' => 'w50',
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'provider' => [
            'inputType' => 'select',
            'exclude' => true,
            'sorting' => true,
            'filter' => true,
            'enum' => Provider::class,
            'eval' => [
                'mandatory' => true,
                'maxlength' => 255,
                'tl_class' => 'w50 clr',
                'includeBlankOption' => true,
                'chosen' => true,
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'verband' => [
            'inputType' => 'select',
            'exclude' => true,
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
        'handballnet_team_id' => [
            'inputType' => 'text',
            'exclude' => true, 
            'eval' => [
                'maxlength' => 255,
                'tl_class' => 'w50',
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'handballnet_tournament_id' => [
            'inputType' => 'text',
            'exclude' => true, 
            'eval' => [
                'maxlength' => 255,
                'tl_class' => 'w50',
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],

        'my_team_name' => [
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
        'is_active' => [
            'toggle' => true,
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 m12'],
            'sql' => "char(1) NOT NULL default ''",
        ],
   
    ],
];
