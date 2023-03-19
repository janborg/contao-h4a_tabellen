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

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

$GLOBALS['TL_DCA']['tl_h4ajsondata'] =
[
    // Config
    'config' => [
        'dataContainer' => 'Table',
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],
    'list' => [
        'sorting' => [
            'mode' => DataContainer::MODE_SORTED,
            'flag' => DataContainer::SORT_DESC,
            'fields' => ['season'],
            'panelLayout' => 'search, sort;filter,limit',
        ],
        'label' => [
            'fields' => ['gClassName', 'lvTypeLabelStr'],
            'format' => '%s - %s',
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
                'label' => &$GLOBALS['TL_LANG']['tl_h4ajsondata']['edit'],
                'href' => 'act=edit',
                'icon' => 'edit.svg',
            ],
            'delete' => [
                'label' => &$GLOBALS['TL_LANG']['tl_h4ajsondata']['delete'],
                'href' => 'act=delete',
                'icon' => 'delete.svg',
                'attributes' => 'onclick="if(!confirm(\''.($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null).'\'))return false;Backend.getScrollOffset()"',
            ],
            'show' => [
                'label' => &$GLOBALS['TL_LANG']['tl_h4ajsondata']['show'],
                'href' => 'act=show',
                'icon' => 'show.svg',
            ],
        ],
    ],
    // Palettes
    'palettes' => [
        'default' => '{title_legend}, lvTypePathStr, lvIDPathStr, lvTypeLabelStr, gClassID, gClassName, DateStart, season, gClassNameLong, gTableJson, gTeamJson',
    ],
    // Fields
    'fields' => [
        'id' => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'tstamp' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'lvTypePathStr' => [
            'label' => &$GLOBALS['TL_LANG']['tl_h4ajsondata']['lvTypePathStr'],
            'exclude' => true,
            'inputType' => 'select',
            'options' => ['class', 'team'],
            'eval' => ['includeBlankOption' => true, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'lvIDPathStr' => [
            'label' => &$GLOBALS['TL_LANG']['tl_h4ajsondata']['lvIDPathStr'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 6, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'lvTypeLabelStr' => [
            'label' => &$GLOBALS['TL_LANG']['tl_h4ajsondata']['lvTypeLabelStr'],
            'exclude' => true,
            'sorting' => true,
            'search' => true,
            'filter' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'gClassID' => [
            'label' => &$GLOBALS['TL_LANG']['tl_h4ajsondata']['gClassID'],
            'exclude' => true,
            'sorting' => true,
            'filter' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NULL default ''",
        ],
        'gClassName' => [
            'label' => &$GLOBALS['TL_LANG']['tl_h4ajsondata']['gClassName'],
            'exclude' => true,
            'sorting' => true,
            'filter' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NULL default ''",
        ],
        'DateStart' => [
            'label' => &$GLOBALS['TL_LANG']['tl_h4ajsondata']['DateStart'],
            'exclude' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'date', 'tl_class' => 'w50', 'datepicker' => true],
            'sql' => "varchar(10) NULL default ''",
        ],
        'season' => [
            'label' => &$GLOBALS['TL_LANG']['tl_h4ajsondata']['season'],
            'exclude' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NULL default ''",
        ],
        'gClassNameLong' => [
            'label' => &$GLOBALS['TL_LANG']['tl_h4ajsondata']['gClassNameLong'],
            'exclude' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NULL default ''",
        ],
        'gTableJson' => [
            'label' => &$GLOBALS['TL_LANG']['tl_h4ajsondata']['gTableJson'],
            'exclude' => true,
            'sorting' => false,
            'inputType' => 'textarea',
            'eval' => ['tl_class' => 'clr'],
            'sql' => "text NOT NULL default ''",
        ],
        'gTeamJson' => [
            'label' => &$GLOBALS['TL_LANG']['tl_h4ajsondata']['gTeamJson'],
            'exclude' => true,
            'sorting' => false,
            'inputType' => 'textarea',
            'eval' => ['tl_class' => 'clr'],
            'sql' => "text NOT NULL default ''",
        ],
    ],
];
