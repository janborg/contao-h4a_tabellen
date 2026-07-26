<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

use Contao\ArrayUtil;
use Contao\BackendUser;
use Janborg\H4aTabellen\HandballNet\Verband;
use Janborg\H4aTabellen\HandballNet\Provider;
use Contao\CoreBundle\DataContainer\PaletteManipulator;

/*
 * Global Operation(s)
 */

ArrayUtil::arrayInsert(
    $GLOBALS['TL_DCA']['tl_calendar']['list']['global_operations'],
    1,
    [
        'h4a_update_calendars' => [
            'label' => &$GLOBALS['TL_LANG']['tl_calendar']['operation_h4a_update_calendars'],
            'class' => 'header_h4a',
            'href' => 'key=h4a_update_calendars',
            'icon' => 'bundles/janborgh4atabellen/update.svg',
        ],
        'h4a_update_results' => [
            'label' => &$GLOBALS['TL_LANG']['tl_calendar']['operation_h4a_update_results'],
            'class' => 'header_h4a',
            'href' => 'key=h4a_update_results',
            'icon' => 'bundles/janborgh4atabellen/update.svg',
        ]
    ]
);

/*
 * Extend palettes
 */
PaletteManipulator::create()
    ->addLegend('h4a_legend', 'title_legend', PaletteManipulator::POSITION_AFTER)
    ->addField('h4a_imported', 'h4a_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_calendar')
;

/*
 * Add Selector(s)
 */

$GLOBALS['TL_DCA']['tl_calendar']['palettes']['__selector__'] = array_merge(
    [
        'h4a_imported',
    ],
    $GLOBALS['TL_DCA']['tl_calendar']['palettes']['__selector__'],
);

/*
 * Create Subpalette(s)
 */

$GLOBALS['TL_DCA']['tl_calendar']['subpalettes'] = array_merge(
    [
        'h4a_imported' => 'h4a_seasons, h4aEvents_author',
    ],
    $GLOBALS['TL_DCA']['tl_calendar']['subpalettes'],
);

/*
 * Table tl_calendar
 */
$GLOBALS['TL_DCA']['tl_calendar']['fields'] = array_merge(
    ['h4a_imported' => [
        'filter' => true,
        'inputType' => 'checkbox',
        'eval' => ['submitOnChange' => true],
        'sql' => "char(1) NOT NULL default ''",
    ]],
    ['h4a_seasons' => [
        'inputType' => 'group',
        'palette' => ['h4a_saison', 'handballnet_id', 'liga_shortname', 'liga_name', 'provider', 'verband'],
        'sql' => 'blob NULL',
    ]],
    ['h4a_saison' => [
        'inputType' => 'select',
        'eval' => [
            'mandatory' => true,
            'tl_class' => 'w50',
            'includeBlankOption' => true,
            'chosen' => true,
        ],
    ]],
    ['handballnet_id' => [
        'inputType' => 'text',
        'eval' => [
            'mandatory' => true,
            'maxlength' => 255,
            'tl_class' => 'w50',
        ],
    ]],
    ['liga_shortname' => [
        'inputType' => 'text',
        'eval' => [
            'maxlength' => 255,
            'tl_class' => 'w50',
        ],
    ]],
    ['liga_name' => [
        'inputType' => 'text',
        'eval' => [
            'maxlength' => 255,
            'tl_class' => 'w50',
        ],
    ]],
    ['provider' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar']['provider'],
        'inputType' => 'select',
        'enum' => Provider::class,
        'eval' => [
            'maxlength' => 255,
            'tl_class' => 'w50',
            'includeBlankOption' => true,
            'chosen' => true,
        ],
    ]],
    ['verband' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar']['verband'],
        'inputType' => 'select',
        'enum' => Verband::class,
        'eval' => [
            'maxlength' => 255,
            'tl_class' => 'w50',
            'includeBlankOption' => true,
            'chosen' => true,
        ],
    ]],
    ['h4aEvents_author' => [
        'default' => BackendUser::getInstance()->id,
        'filter' => true,
        'sorting' => true,
        'flag' => 1,
        'inputType' => 'select',
        'foreignKey' => 'tl_user.name',
        'eval' => [
            'doNotCopy' => true,
            'chosen' => true,
            'mandatory' => true,
            'includeBlankOption' => true,
            'cols' => 4,
            'tl_class' => 'w50',
        ],
        'sql' => "int(10) unsigned NOT NULL default '0'",
        'relation' => [
            'type' => 'hasOne',
            'load' => 'eager',
        ],
    ]],
    $GLOBALS['TL_DCA']['tl_calendar']['fields'],
);
