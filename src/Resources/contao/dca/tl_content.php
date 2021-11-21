<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

use Contao\System;

System::loadLanguageFile('tl_h4a');

/*
 * Palettes
 */

$GLOBALS['TL_DCA']['tl_content']['palettes']['h4a_tabelle'] = '{type_legend},type,headline;{h4a_legend},h4a_liga_ID, my_team_name';
$GLOBALS['TL_DCA']['tl_content']['palettes']['h4a_spiele'] = '{type_legend},type,headline;{h4a_legend},h4a_team_ID, my_team_name';
$GLOBALS['TL_DCA']['tl_content']['palettes']['h4a_aktuellespiele'] = '{type_legend},type,headline;{h4a_legend},h4a_verein_ID, my_team_name';
$GLOBALS['TL_DCA']['tl_content']['palettes']['h4a_ligaspiele'] = '{type_legend},type,headline;{h4a_legend},h4a_liga_ID, my_team_name';
/*
 * Fields
 */

$GLOBALS['TL_DCA']['tl_content']['fields']['h4a_liga_ID'] = [
    'label' => $GLOBALS['TL_LANG']['tl_content']['h4a_liga_ID'],
    'inputType' => 'text',
    'exclude' => true,
    'eval' => [
        'mandatory' => true,
        'rgxp' => 'digit',
        'minlenght' => 5,
        'maxlength' => 5,
        'tl_class' => 'w50',
    ],
    'sql' => "varchar(255) NOT NULL default ''",
];
$GLOBALS['TL_DCA']['tl_content']['fields']['h4a_team_ID'] = [
    'label' => $GLOBALS['TL_LANG']['tl_content']['h4a_team_ID'],
    'inputType' => 'text',
    'exclude' => true,
    'eval' => [
        'mandatory' => true,
        'rgxp' => 'digit',
        'minlenght' => 6,
        'maxlength' => 6,
        'tl_class' => 'w50',
    ],
    'sql' => "varchar(255) NOT NULL default ''",
];
$GLOBALS['TL_DCA']['tl_content']['fields']['h4a_verein_ID'] = [
    'label' => $GLOBALS['TL_LANG']['tl_content']['h4a_verein_ID'],
    'inputType' => 'text',
    'exclude' => true,
    'eval' => [
        'mandatory' => true,
        'rgxp' => 'digit',
        'minlenght' => 1,
        'maxlength' => 4,
        'tl_class' => 'w50',
    ],
    'sql' => "varchar(255) NOT NULL default ''",
];
$GLOBALS['TL_DCA']['tl_content']['fields']['my_team_name'] = [
    'label' => $GLOBALS['TL_LANG']['tl_content']['my_team_name'],
    'inputType' => 'text',
    'exclude' => true,
    'eval' => [
        'mandatory' => true,
        'unique' => false,
        'maxlength' => 255,
        'tl_class' => 'w50',
    ],
    'sql' => "varchar(255) NOT NULL default ''",
];
