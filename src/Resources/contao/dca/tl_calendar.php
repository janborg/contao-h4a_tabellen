<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

use Contao\BackendUser;
use Contao\CoreBundle\DataContainer\PaletteManipulator;

/*
 * Global Operation(s)
 */

$GLOBALS['TL_DCA']['tl_calendar']['list']['global_operations'] = array_merge(
    ['h4a_update_calendars' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar']['operation_h4a_update_calendars'],
        'class' => 'header_h4a',
        'href' => 'key=h4a_update_calendars',
        'icon' => 'bundles/janborgh4atabellen/update.svg',
    ]],
    ['h4a_update_results' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar']['operation_h4a_update_results'],
        'class' => 'header_h4a',
        'href' => 'key=h4a_update_results',
        'icon' => 'bundles/janborgh4atabellen/update.svg',
    ]],
    ['h4a_seasons' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar']['operation_h4a_seasons'],
        'class' => 'header_h4a',
        'href' => 'table=tl_h4a_seasons',
        'icon' => 'bundles/janborgh4atabellen/seasons.svg',
        'attr' => 'onclick="Backend.getScrollOffset()"',
    ]],
    $GLOBALS['TL_DCA']['tl_calendar']['list']['global_operations']
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
    $GLOBALS['TL_DCA']['tl_calendar']['palettes']['__selector__']
);

/*
 * Create Subpalette(s)
 */

$GLOBALS['TL_DCA']['tl_calendar']['subpalettes'] = array_merge(
    [
        'h4a_imported' => 'h4a_seasons, h4aEvents_author',
    ],
    $GLOBALS['TL_DCA']['tl_calendar']['subpalettes']
);

/*
 * Table tl_calendar
 */
$GLOBALS['TL_DCA']['tl_calendar']['fields'] = array_merge(
    ['h4a_imported' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar']['h4a_imported'],
        'exclude' => true,
        'filter' => true,
        'inputType' => 'checkbox',
        'eval' => ['submitOnChange' => true],
        'sql' => "char(1) NOT NULL default ''",
    ]],
    ['h4a_seasons' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar']['h4a_saison'],
        'exclude' => false,
        'inputType' => 'group',
        'palette' => ['h4a_saison', 'h4a_team', 'h4a_liga', 'my_team_name'],
        'fields' => [
            'h4a_saison' => [
                'label' => &$GLOBALS['TL_LANG']['tl_calendar']['h4a_saison'],
                'inputType' => 'select',
                'foreignKey' => 'tl_h4a_seasons.season',
                'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
                'eval' => [
                    'mandatory' => true,
                    'tl_class' => 'w50',
                    'includeBlankOption' => true,
                    'chosen' => true,
                ],
            ],
            'h4a_team' => [
                'label' => &$GLOBALS['TL_LANG']['tl_calendar']['h4a_team'],
                'inputType' => 'text',
                'eval' => [
                    'mandatory' => true,
                    'rgxp' => 'digit',
                    'maxlength' => 6,
                    'tl_class' => 'w50',
                ],
            ],
            'h4a_liga' => [
                'label' => &$GLOBALS['TL_LANG']['tl_calendar']['h4a_liga_ID'],
                'inputType' => 'text',
                'eval' => [
                    'mandatory' => true,
                    'rgxp' => 'digit',
                    'maxlength' => 5,
                    'tl_class' => 'w50',
                ],
            ],
            'my_team_name' => [
                'label' => &$GLOBALS['TL_LANG']['tl_calendar']['my_team_name'],
                'inputType' => 'text',
                'eval' => [
                    'mandatory' => true,
                    'maxlength' => 255,
                    'tl_class' => 'w50',
                ],
            ],
        ],
        'sql' => 'blob NULL',
    ]],
    ['h4aEvents_author' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar']['h4aEvents_author'],
        'default' => BackendUser::getInstance()->id,
        'exclude' => true,
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
    $GLOBALS['TL_DCA']['tl_calendar']['fields']
);
