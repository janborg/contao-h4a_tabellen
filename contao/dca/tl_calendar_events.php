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

/*
 * Global Operation(s)
 */
ArrayUtil::arrayInsert($GLOBALS['TL_DCA']['tl_calendar_events']['list']['global_operations'], 
1, [
    'update_handballnet_events' => [
        'class' => 'header_h4a',
        'href' => 'key=update_handballnet_events',
        'icon' => 'bundles/janborgh4atabellen/refresh.svg',
        'primary' => true
    ]],
);
    

/*
 * Table tl_calendar_events
 */

$GLOBALS['TL_DCA']['tl_calendar_events']['fields'] = array_merge(
    ['gGameID' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gGameID'],
        'search' => true,
        'inputType' => 'text',
        'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''",
    ]],
    ['gGameNo' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gGameNo'],
        'search' => true,
        'inputType' => 'text',
        'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''",
    ]],
    ['gClassName' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gClassName'],
        'search' => true,
        'inputType' => 'text',
        'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''",
    ]],
    ['liga_name' => [
        'inputType' => 'text',
        'sorting' => true,
        'filter' => true,
        'search' => true,
        'eval' => [
            'mandatory' => true,
            'maxlength' => 255,
            'tl_class' => 'w50',
        ],
        'sql' => "varchar(255) NOT NULL default ''",
    ]],
    ['provider' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar']['provider'],
        'inputType' => 'text',
        'eval' => [
            'mandatory' => true,
            'maxlength' => 255,
            'tl_class' => 'w50',
        ],
        'sql' => "varchar(255) NOT NULL default ''",
    ]],
    ['verband' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar']['verband'],
        'inputType' => 'text',
        'eval' => [
            'mandatory' => true,
            'maxlength' => 255,
            'tl_class' => 'w50',
        ],
        'sql' => "varchar(255) NOT NULL default ''",
    ]],
    ['gHomeTeam' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gHomeTeam'],
        'search' => true,
        'inputType' => 'text',
        'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''",
    ]],
    ['gGuestTeam' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gGuestTeam'],
        'search' => true,
        'inputType' => 'text',
        'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''",
    ]],
    ['gGymnasiumNo' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gGymnasiumNo'],
        'search' => true,
        'inputType' => 'text',
        'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
        'sql' => "varchar(255) NULL default ''",
    ]],
    ['gGymnasiumName' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gGymnasiumName'],
        'search' => true,
        'inputType' => 'text',
        'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
        'sql' => "varchar(255) NULL default ''",
    ]],
    ['gGymnasiumStreet' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gGymnasiumStreet'],
        'search' => true,
        'inputType' => 'text',
        'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
        'sql' => "varchar(255) NULL default ''",
    ]],
    ['gGymnasiumTown' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gGymnasiumTown'],
        'search' => true,
        'inputType' => 'text',
        'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
        'sql' => "varchar(255) NULL default ''",
    ]],
    ['gGymnasiumPostal' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gGymnasiumPostal'],
        'search' => true,
        'inputType' => 'text',
        'eval' => ['mandatory' => false, 'maxlength' => 5, 'tl_class' => 'w50'],
        'sql' => "varchar(255) NULL default ''",
    ]],
    ['gHomeGoals' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gHomeGoals'],
        'search' => false,
        'inputType' => 'text',
        'eval' => ['mandatory' => false, 'maxlength' => 3, 'rgxp' => 'digit', 'tl_class' => 'w50'],
        'sql' => "varchar(255) NULL default ''",
    ]],
    ['gGuestGoals' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gGuestGoals'],
        'search' => false,
        'inputType' => 'text',
        'eval' => ['mandatory' => false, 'maxlength' => 3, 'rgxp' => 'digit', 'tl_class' => 'w50'],
        'sql' => "varchar(255) NULL default ''",
    ]],
    ['gHomeGoals_1' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gHomeGoals_1'],
        'search' => false,
        'inputType' => 'text',
        'eval' => ['mandatory' => false, 'maxlength' => 3, 'rgxp' => 'digit', 'tl_class' => 'w50'],
        'sql' => "varchar(255) NULL default ''",
    ]],
    ['gGuestGoals_1' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gGuestGoals_1'],
        'search' => false,
        'inputType' => 'text',
        'eval' => ['mandatory' => false, 'maxlength' => 3, 'rgxp' => 'digit', 'tl_class' => 'w50'],
        'sql' => "varchar(255) NULL default ''",
    ]],
    ['h4a_resultComplete' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['h4a_resultComplete'],
        'filter' => true,
        'inputType' => 'checkbox',
        'eval' => ['tl_class' => 'w50 m12'],
        'sql' => "char(1) NOT NULL default ''",
    ]],
    ['gComment' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gComment'],
        'search' => false,
        'inputType' => 'text',
        'eval' => ['mandatory' => false, 'tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''",
    ]],
    ['sGID' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['sGID'],
        'search' => true,
        'inputType' => 'text',
        'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''",
    ]],
    ['h4a_season' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar']['h4a_season'],
        'inputType' => 'select',
        'foreignKey' => 'tl_h4a_seasons.season',
        'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
        'filter' => true,
        'eval' => [
            'mandatory' => true,
            'unique' => false,
            'tl_class' => 'w50',
        ],
        'sql' => "varchar(9) NOT NULL default ''",
    ]],
    ['handballnet_id' => [
        'label' =>&$GLOBALS['TL_LANG']['tl_calendar_events']['handballnet_id'],
        'inputType' => 'text',
        'eval' => [
            'mandatory' => false,
            'unique' => true,
            'tl_class' => 'w50',
        ],
        'sql' => "varchar(255) NOT NULL default ''",
    ]],
    $GLOBALS['TL_DCA']['tl_calendar_events']['fields'],
);
