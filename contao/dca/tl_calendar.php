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
    ->addLegend('handballnet_legend', 'title_legend', PaletteManipulator::POSITION_AFTER)
    ->addField('handballnet_imported', 'handballnet_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_calendar')
;

/*
 * Add Selector(s)
 */

$GLOBALS['TL_DCA']['tl_calendar']['palettes']['__selector__'] = array_merge(
    [
        'handballnet_imported',
    ],
    $GLOBALS['TL_DCA']['tl_calendar']['palettes']['__selector__'],
);

/*
 * Create Subpalette(s)
 */

$GLOBALS['TL_DCA']['tl_calendar']['subpalettes'] = array_merge(
    [
        'handballnet_imported' => 'handballnet_club,handballnet_season,handballnet_team_id,add_team_group_id_events,h4aEvents_author',
    ],
    $GLOBALS['TL_DCA']['tl_calendar']['subpalettes'],
);

/*
 * Fields
 */
$GLOBALS['TL_DCA']['tl_calendar']['fields']['handballnet_club'] = [
    'foreignKey' => 'tl_hn_clubs.name',
    'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
    'inputType' => 'select',
    'eval' => [
        'mandatory' => true,
        'tl_class' => 'w33',
        'includeBlankOption' => true,
        'chosen' => true,
        'submitOnChange' => true,
    ],
    'sql' => "varchar(10) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_calendar']['fields']['handballnet_season'] = [
    'inputType' => 'select',
    'eval' => [
        'mandatory' => true,
        'tl_class' => 'w33',
        'includeBlankOption' => true,
        'chosen' => true,
        'submitOnChange' => true,
    ],
    'sql' => "varchar(10) NOT NULL default ''",
];
$GLOBALS['TL_DCA']['tl_calendar']['fields']['handballnet_team_id'] = [
    'inputType' => 'select',
    'eval' => [
        'mandatory' => true,
        'includeBlankOption' => true,
        'chosen' => true,
        'tl_class' => 'w33',
    ],
    'sql' => "varchar(255) NOT NULL default ''",
];
$GLOBALS['TL_DCA']['tl_calendar']['fields']['handballnet_imported'] = [
    'filter' => true,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true, 'tl_class' => 'w33 m12'],
    'sql' => ['type' => 'boolean', 'default' => false],
];
$GLOBALS['TL_DCA']['tl_calendar']['fields']['h4aEvents_author'] = [
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
        'tl_class' => 'w33',
    ],
    'sql' => "int(10) unsigned NOT NULL default '0'",
    'relation' => [
        'type' => 'hasOne',
        'load' => 'eager',
    ],
];
$GLOBALS['TL_DCA']['tl_calendar']['fields']['add_team_group_id_events'] = [
    'filter' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w33 m12'],
    'sql' => ['type' => 'boolean', 'default' => false],
];
