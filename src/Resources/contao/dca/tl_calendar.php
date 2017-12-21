<?php

/*
 * Extend palettes
 */
\Contao\CoreBundle\DataContainer\PaletteManipulator::create()
    ->addLegend('h4a_legend', 'title_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_AFTER)
    ->addField('h4a_imported','h4a_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_calendar');

/*
 * Add Selector(s)
 */
	
$GLOBALS['TL_DCA']['tl_calendar']['palettes']['__selector__'] = array_merge(
		array('h4a_imported'
		),
		$GLOBALS['TL_DCA']['tl_calendar']['palettes']['__selector__']
);

	
/*
 * Create Subpalette(s)
 */
 
$GLOBALS['TL_DCA']['tl_calendar']['subpalettes'] = array_merge(
		array('h4a_imported' => 'h4a_liga_ID, h4a_team_ID, my_team_name'
		),
		$GLOBALS['TL_DCA']['tl_calendar']['subpalettes']
);
	
/**
 * Table tl_calendar
 */
$GLOBALS['TL_DCA']['tl_calendar']['fields'] = array_merge(
        array('h4a_imported' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_calendar']['h4a_imported'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('submitOnChange'=>true),
			'sql'                     => "char(1) NOT NULL default ''"
		)),
		array('h4a_liga_ID' => array(
			'label'     => $GLOBALS['TL_LANG']['tl_calendar']['h4a_liga_ID'],
			'inputType'	=> 'text',
			'exclude'	=> true,
			'eval'	=> array(
				'mandatory'     => true,
				'rgxp'          => 'digit',
				'minlenght'     => 5,
				'maxlength'     => 5,
				'tl_class'      => 'w50',
			),
			'sql'	=>	"varchar(255) NOT NULL default ''",
		)),
		array('h4a_team_ID' => array(
			'label'     => $GLOBALS['TL_LANG']['tl_calendar']['h4a_team_ID'],
			'inputType'	=> 'text',
			'exclude'	=> true,
			'eval'	=> array(
				'mandatory'     => true,
				'rgxp'          => 'digit',
				'minlenght'     => 6,
				'maxlength'     => 6,
				'tl_class'      => 'w50',
			),
			'sql'	=>	"varchar(255) NOT NULL default ''",
		)),
		array('my_team_name' => array(
			'label'     => $GLOBALS['TL_LANG']['tl_calendar']['my_team_name'],
			'inputType'	=> 'text',
			'exclude'	=> true,
			'eval'	=> array(
				'mandatory'     => true,
				'unique'        => false,
				'maxlength'     => 255,
				'tl_class'      => 'w50',
			),
			'sql'	=>	"varchar(255) NOT NULL default ''"
		)),
        $GLOBALS['TL_DCA']['tl_calendar']['fields']
    );
	
