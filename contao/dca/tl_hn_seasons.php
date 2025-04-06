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

$GLOBALS['TL_DCA']['tl_hn_seasons'] = [
    // Config
    'config' => [
        'dataContainer' => DC_Table::class,
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
        'backlink' => 'do=hn_teams',
    ],
    'list' => [
        'sorting' => [
            'mode' => DataContainer::MODE_SORTED,
            'flag' => DataContainer::SORT_DESC,
            'fields' => ['season'],
            'panelLayout' => 'search, sort;filter,limit',
        ],
        'label' => [
            'fields' => ['club_name', 'club_id', 'provider', 'verband'],
            'format' => '%s ( %s, %s, %s )',
        ],

        'global_operations' => [
            'all' => [
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"',
            ],
        ],
        'operations' => [
            'edit' => [
                'href' => 'act=edit',
                'icon' => 'edit.svg',
            ],
            'delete' => [
                'href' => 'act=delete',
                'icon' => 'delete.svg',
                'attributes' => 'onclick="if(!confirm(\''.($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null).'\'))return false;Backend.getScrollOffset()"',
            ],
            'show' => [
                'href' => 'act=show',
                'icon' => 'show.svg',
            ],
            'toggle' => [
				'href'                => 'act=toggle&amp;field=is_active',
				'icon'                => 'visible.svg',
				'showInHeader'        => true
			],
        ],
    ],
    // Palettes
    'palettes' => [
        'default' => '{title_legend}, season, is_active, club_id, club_name, provider, verband',
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
            'exclude' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => [
                'maxlength' => 4, 
                'tl_class' => 'w50',
                'rgxp' => 'digit',
            ],
            'sql' => "varchar(4) NOT NULL default ''",
        ],
        'club_id' => [
            'inputType' => 'text',
            'exclude' => true,
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
        'provider' => [
            'inputType' => 'select',
            'exclude' => true,
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
