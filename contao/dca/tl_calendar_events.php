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
    ['gClassName' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gClassName'],
        'search' => true,
        'inputType' => 'text',
        'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
        'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
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
        'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
    ]],
    ['homeTeam_name' => [
        'search' => true,
        'inputType' => 'text',
        'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
        'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
    ]],
    ['homeTeam_id' => [
        'search' => true,
        'inputType' => 'text',
        'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
        'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
    ]],
    ['awayTeam_name' => [
        'search' => true,
        'inputType' => 'text',
        'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
        'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
    ]],
    ['awayTeam_id' => [
        'search' => true,
        'inputType' => 'text',
        'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
        'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
    ]],
    ['handballnet_tournament_id' => [
        'inputType' => 'text',
        'eval' => [
            'mandatory' => false,
            'tl_class' => 'w50',
        ],
        'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
    ]],
    ['handballnet_tournament_name' => [
        'inputType' => 'text',
        'eval' => [
            'mandatory' => false,
            'tl_class' => 'w50',
        ],
        'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
    ]],
    ['handballnet_phase_id' => [
        'inputType' => 'text',
        'eval' => [
            'mandatory' => false,
            'tl_class' => 'w50',
        ],
        'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
    ]],
    ['handballnet_phase_name' => [
        'inputType' => 'text',
        'eval' => [
            'mandatory' => false,
            'tl_class' => 'w50',
        ],
        'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
    ]],
    ['handballnet_round_id' => [
        'inputType' => 'text',
        'eval' => [
            'mandatory' => false,
            'tl_class' => 'w50',
        ],
        'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
    ]],
    ['handballnet_round_name' => [
        'inputType' => 'text',
        'eval' => [
            'mandatory' => false,
            'tl_class' => 'w50',
        ],
        'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
    ]],
    ['handballnet_field_id' => ['inputType' => 'text',
        'eval' => [
            'mandatory' => false,
            'tl_class' => 'w50',
        ],
        'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
    ]],
    ['homeGoals' => [
        'search' => false,
        'inputType' => 'text',
        'eval' => ['mandatory' => false, 'maxlength' => 3, 'rgxp' => 'digit', 'tl_class' => 'w50'],
        'sql' => ['type' => 'string', 'length' => 255, 'default' => '', 'notnull' => false],
    ]],
    ['awayGoals' => [
        'search' => false,
        'inputType' => 'text',
        'eval' => ['mandatory' => false, 'maxlength' => 3, 'rgxp' => 'digit', 'tl_class' => 'w50'],
        'sql' => ['type' => 'string', 'length' => 255, 'default' => '', 'notnull' => false],
    ]],
    ['homeGoalsHalf' => [
        'search' => false,
        'inputType' => 'text',
        'eval' => ['mandatory' => false, 'maxlength' => 3, 'rgxp' => 'digit', 'tl_class' => 'w50'],
        'sql' => ['type' => 'string', 'length' => 255, 'default' => '', 'notnull' => false],
    ]],
    ['awayGoalsHalf' => [
        'search' => false,
        'inputType' => 'text',
        'eval' => ['mandatory' => false, 'maxlength' => 3, 'rgxp' => 'digit', 'tl_class' => 'w50'],
        'sql' => ['type' => 'string', 'length' => 255, 'default' => '', 'notnull' => false],
    ]],
    //['handballnet_state' => []],
    ['sGID' => [
        'search' => true,
        'inputType' => 'text',
        'eval' => ['mandatory' => false, 'maxlength' => 50, 'tl_class' => 'w50'],
        'sql' => ['type' => 'string', 'length' => 50, 'default' => ''],
    ]],
    ['handballnet_season' => [
        'inputType' => 'select',
        'filter' => true,
        'eval' => [
            'readonly' => true,
            'tl_class' => 'w50',
        ],
        'sql' => ['type' => 'string', 'length' => 9, 'default' => ''],
    ]],
    ['handballnet_game_id' => [
        'inputType' => 'text',
        'eval' => [
            'mandatory' => false,
            'unique' => true,
            'tl_class' => 'w50',
        ],
        'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
    ]],
    ['hn_resultComplete' => [
        'filter' => true,
        'inputType' => 'checkbox',
        'eval' => ['tl_class' => 'w50 m12'],
        'sql' => ['type' => 'boolean', 'default' => false]
    ]],
    $GLOBALS['TL_DCA']['tl_calendar_events']['fields'],
);
