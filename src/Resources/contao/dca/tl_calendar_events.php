<?php


/*
 * Extend palettes
 */
\Contao\CoreBundle\DataContainer\PaletteManipulator::create()
    ->addLegend('h4a_legend', 'title_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_AFTER)
    ->addField('gGameNo,gClassName,gHomeTeam,gGuestTeam,gResult,gHalfResult','h4a_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_calendar_events');
 
/*
 * Add Selector(s)
 */
 
/*
 * Create Subpalette(s)
 */
 
/*
 * Global Operation(s)
 */
 
 $GLOBALS['TL_DCA']['tl_calendar_events']['list']['global_operations'] = array_merge(
	array('h4a_import' => array(
            'label'               => &$GLOBALS['TL_LANG']['tl_calendar_events']['operationImportFromH4a'],
            'href'                => 'act=paste&mode=move&source=h4a',
            'class'               => 'header_h4a',
            'icon'                => '',
            'button_callback'     => array('tl_calendar_events_h4a', 'h4a_import')
        )),
	$GLOBALS['TL_DCA']['tl_calendar_events']['list']['global_operations']
 );
	
/**
 * Table tl_calendar_events
 */

 $GLOBALS['TL_DCA']['tl_calendar_events']['fields'] = array_merge(
        array('gGameNo' => array(
			'label'                   => &$GLOBALS['TL_LANG']['tl_calendar_events']['gGameNo'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		)),
		array('gClassName' => array(
			'label'                   => &$GLOBALS['TL_LANG']['tl_calendar_events']['gClassName'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		)),
        array('gHomeTeam' => array(
			'label'                   => &$GLOBALS['TL_LANG']['tl_calendar_events']['gHomeTeam'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		)),
		array('gGuestTeam' => array(
			'label'                   => &$GLOBALS['TL_LANG']['tl_calendar_events']['gGuestTeam'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		)),
		array('gResult' => array(
			'label'                   => &$GLOBALS['TL_LANG']['tl_calendar_events']['gResult'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		)),
		array('gHalfResult' => array(
			'label'                   => &$GLOBALS['TL_LANG']['tl_calendar_events']['gHalfResult'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		)),
        $GLOBALS['TL_DCA']['tl_calendar_events']['fields']
    );
	

/**
 * Class tl_calendar_events_h4a
 */
class tl_calendar_events_h4a extends Backend
{

    /**
     * tl_calendar_events_h4a constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->import('BackendUser', 'User');
    }


    /**
     * @param $href
     * @param $label
     * @param $title
     * @param $class
     * @param $attributes
     * @return string
     */
    public function h4a_import($href, $label, $title, $class, $attributes)
    {
        return '<a href="'.$this->addToUrl($href).'" title="'.StringUtil::specialchars($title).'" class="'.$class.'"'.$attributes.'>'.$label.'</a> ' ;
    }
}
