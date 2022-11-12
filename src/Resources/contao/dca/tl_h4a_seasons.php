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


$GLOBALS['TL_DCA']['tl_h4a_seasons'] = [
    // Config
    'config' => [
        'dataContainer' => DC_Table::class,
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
        'backlink' => 'do=calendar'
    ],
    'list' => [
        'sorting' => [
            'mode' => DataContainer::MODE_SORTED,
            'flag' => DataContainer::SORT_INITIAL_LETTER_ASC,
            'fields' => ['season'],
            'panelLayout' => 'search, sort;filter,limit',
        ],
        'label' => [
            'fields' => ['season'],
            'format' => '%s',
        ],

        'global_operations' => [
            'all' => [
                'label' => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"',
            ],
        ],
        'operations' => [
            'edit' => [
                'label' => &$GLOBALS['TL_LANG']['tl_h4a_seasons']['edit'],
                'href' => 'act=edit',
                'icon' => 'edit.svg',
            ],
            'delete' => [
                'label' => &$GLOBALS['TL_LANG']['tl_h4a_seasons']['delete'],
                'href' => 'act=delete',
                'icon' => 'delete.svg',
                'attributes' => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null) . '\'))return false;Backend.getScrollOffset()"',
            ],
            'show' => [
                'label' => &$GLOBALS['TL_LANG']['tl_h4a_seasons']['show'],
                'href' => 'act=show',
                'icon' => 'show.svg',
            ],

            'toggle_ignore' => [
                'label' => &$GLOBALS['TL_LANG']['tl_calendar']['toggle_ignore'],
                'attributes' => 'onclick="Backend.getScrollOffset();"',
                'haste_ajax_operation' => [
                    'field' => 'h4a_ignore',
                    'options' => [
                        [
                            'value' => '',
                            'icon' => 'visible.gif',
                        ],
                        [
                            'value' => '1',
                            'icon' => 'delete.gif',
                        ],
                    ],
                ],
            ],
        ],
    ],
    // Palettes
    'palettes' => [
        'default' => '{title_legend}, season, is_current_season',
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
            'exclude' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 9, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NULL default ''",
        ],
        'is_current_season' => [
            'label' => &$GLOBALS['TL_LANG']['tl_h4a_season']['is_current_season'],
            'default' => 0,
            'unique' => true,
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50'],
            'sql' => "char(1) NOT NULL default ''",
        ],
        'h4a_ignore' => [
            'label' => &$GLOBALS['TL_LANG']['tl_calendar']['h4a_ignore'],
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 m12'],
            'sql' => "char(1) NOT NULL default ''",
        ],
    ],
];
