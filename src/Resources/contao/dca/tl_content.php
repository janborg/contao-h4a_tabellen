<?php

// Load language file(s)
System::loadLanguageFile('tl_h4a');

/*
 * Palettes
 */

$GLOBALS['TL_DCA']['tl_content']['palettes']['h4a_tabelle'] = '{type_legend},type,headline;{h4a_legend},h4a_liga_ID, my_team_name';
$GLOBALS['TL_DCA']['tl_content']['palettes']['h4a_spiele'] = '{type_legend},type,headline;{h4a_legend},h4a_team_ID, my_team_name';

/*
 * Fields
 */

$GLOBALS['TL_DCA']['tl_content']['fields']['h4a_liga_ID'] = array(
    'label' => $GLOBALS['TL_LANG']['tl_content']['h4a_liga_ID'],
    'inputType' => 'text',
    'exclude' => true,
    'eval' => array(
        'mandatory' => true,
        'rgxp' => 'digit',
        'minlenght' => 5,
        'maxlength' => 5,
        'tl_class' => 'w50',
    ),
    'sql' => "varchar(255) NOT NULL default ''",
);
$GLOBALS['TL_DCA']['tl_content']['fields']['h4a_team_ID'] = array(
    'label' => $GLOBALS['TL_LANG']['tl_content']['h4a_team_ID'],
    'inputType' => 'text',
    'exclude' => true,
    'eval' => array(
        'mandatory' => true,
        'rgxp' => 'digit',
        'minlenght' => 6,
        'maxlength' => 6,
        'tl_class' => 'w50',
    ),
    'sql' => "varchar(255) NOT NULL default ''",
);
$GLOBALS['TL_DCA']['tl_content']['fields']['my_team_name'] = array(
    'label' => $GLOBALS['TL_LANG']['tl_content']['my_team_name'],
    'inputType' => 'text',
    'exclude' => true,
    'eval' => array(
        'mandatory' => true,
        'unique' => false,
        'maxlength' => 255,
        'tl_class' => 'w50',
    ),
    'sql' => "varchar(255) NOT NULL default ''",
);
