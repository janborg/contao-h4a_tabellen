<?php

// Load language file(s)
System::loadLanguageFile('tl_h4a');


/*
 * Extend palettes
 */
\Contao\CoreBundle\DataContainer\PaletteManipulator::create()
    ->addLegend('h4a_legend', 'chmod_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_AFTER)
    ->addField('h4a_cache_time', 'h4a_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_settings');




/*
 * Fields
 */
 $GLOBALS['TL_DCA']['tl_settings']['fields']['h4a_cache_time'] = array(
     'label' => $GLOBALS['TL_LANG']['tl_settings']['h4a_cache_time'],
     'inputType' => 'text',
     'exclude' => true,
     'eval' => array(
         'mandatory' => true,
         'rgxp' => 'digit',
         'tl_class' => 'w50',
     ),
     'default' => '86400',
     'sql' => "varchar(255) NOT NULL default ''",
 );
