<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

use Contao\CoreBundle\DataContainer\PaletteManipulator;

System::loadLanguageFile('tl_h4a');

/*
 * Extend palettes
 */
PaletteManipulator::create()
    ->addLegend('h4a_legend', 'chmod_legend', PaletteManipulator::POSITION_AFTER)
    ->addField('h4a_cache_time', 'h4a_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_settings')
;

/*
 * Fields
 */
 $GLOBALS['TL_DCA']['tl_settings']['fields']['h4a_cache_time'] = [
     'label' => $GLOBALS['TL_LANG']['tl_settings']['h4a_cache_time'],
     'inputType' => 'text',
     'exclude' => true,
     'eval' => [
         'mandatory' => true,
         'rgxp' => 'digit',
         'tl_class' => 'w50',
     ],
     'default' => '86400',
     'sql' => "varchar(255) NOT NULL default ''",
 ];
