<?php

// Load language file(s)
System::loadLanguageFile('tl_h4a');

/*
 * Global Operation(s)
 */

$GLOBALS['TL_DCA']['tl_calendar']['list']['global_operations'] = array_merge(
    array('h4a_update' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_h4a']['operationImportFromH4a'],
            'class' => 'header_h4a',
			'$href' => 'key=update_events',
            'icon' 	=> 'bundles/janborgh4atabellen/update.svg',
            'button_callback' => array('tl_calendar_h4a', 'h4a_update'),
        )),
    $GLOBALS['TL_DCA']['tl_calendar']['list']['global_operations']
 );

/*
 * Operation(s)
 */
 $GLOBALS['TL_DCA']['tl_calendar']['list']['operations'] = array_merge(
	$GLOBALS['TL_DCA']['tl_calendar']['list']['operations'],
	array('toggle_ignore' => array(
		'label'                 => &$GLOBALS['TL_LANG']['tl_calendar']['toggle_ignore'],
		'attributes'            => 'onclick="Backend.getScrollOffset();"',
		'haste_ajax_operation'  => [
			'field'     => 'h4a_ignore',
			'options'    => [
				[
					'value'     => '',
					'icon'      => 'visible.gif'
				],
				[
					'value'     => '1',
					'icon'      => 'delete.gif'
				]
			]
		]
	))
 );

/*
 * Extend palettes
 */
\Contao\CoreBundle\DataContainer\PaletteManipulator::create()
    ->addLegend('h4a_legend', 'title_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_AFTER)
    ->addField('h4a_imported', 'h4a_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_calendar');

/*
 * Add Selector(s)
 */

$GLOBALS['TL_DCA']['tl_calendar']['palettes']['__selector__'] = array_merge(
        array('h4a_imported',
        ),
        $GLOBALS['TL_DCA']['tl_calendar']['palettes']['__selector__']
);

/*
 * Create Subpalette(s)
 */

$GLOBALS['TL_DCA']['tl_calendar']['subpalettes'] = array_merge(
        array('h4a_imported' => 'h4a_team_ID, my_team_name, h4aEvents_author, h4a_ignore',
        ),
        $GLOBALS['TL_DCA']['tl_calendar']['subpalettes']
);


/*
 * Table tl_calendar
 */
$GLOBALS['TL_DCA']['tl_calendar']['fields'] = array_merge(
        array('h4a_imported' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_calendar']['h4a_imported'],
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => array('submitOnChange' => true),
            'sql' => "char(1) NOT NULL default ''",
        )),
        array('h4a_ignore' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_calendar']['h4a_ignore'],
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => array('tl_class' => 'w50 m12', ),
            'sql' => "char(1) NOT NULL default ''",
        )),
        array('h4a_liga_ID' => array(
            'label' => $GLOBALS['TL_LANG']['tl_calendar']['h4a_liga_ID'],
            'inputType' => 'text',
            'exclude' => true,
            'eval' => array(
                'mandatory' => true,
                'rgxp' => 'digit',
                'minlength' => 5,
                'maxlength' => 5,
                'tl_class' => 'w50',
            ),
            'sql' => "varchar(255) NOT NULL default ''",
        )),
        array('h4a_team_ID' => array(
            'label' => $GLOBALS['TL_LANG']['tl_calendar']['h4a_team_ID'],
            'inputType' => 'text',
            'exclude' => true,
            'eval' => array(
                'mandatory' => true,
                'rgxp' => 'digit',
                'minlength' => 6,
                'maxlength' => 6,
                'tl_class' => 'w50',
            ),
            'sql' => "varchar(255) NOT NULL default ''",
        )),
        array('my_team_name' => array(
            'label' => $GLOBALS['TL_LANG']['tl_calendar']['my_team_name'],
            'inputType' => 'text',
            'exclude' => true,
            'eval' => array(
                'mandatory' => true,
                'unique' => false,
                'maxlength' => 255,
                'tl_class' => 'w50',
            ),
            'sql' => "varchar(255) NOT NULL default ''",
        )),
		array('h4aEvents_author' => array(
			'label' => &$GLOBALS['TL_LANG']['tl_calendar']['h4aEvents_author'],
			'default' => \BackendUser::getInstance()->id,
			'exclude' => true,
			'filter' => true,
			'sorting' => true,
			'flag' => 1,
			'inputType' => 'select',
			'foreignKey' => 'tl_user.name',
			'eval' => array(
				'doNotCopy' => true,
				'chosen' => true,
				'mandatory' => true,
				'includeBlankOption' => true,
				'cols' => 4,
				'tl_class' => 'w50',
			),
			'sql' => "int(10) unsigned NOT NULL default '0'",
			'relation' => array(
				'type' => 'hasOne',
				'load' => 'eager',
			)
	   )),
        $GLOBALS['TL_DCA']['tl_calendar']['fields']
    );


/**
 * Class tl_calendar_h4a.
 */
class tl_calendar_h4a extends Backend
{
    /**
     * tl_calendar_h4a constructor.
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
     *
     * @return string
     */
    public function h4a_update($href, $label, $title, $class, $attributes)
    {
		$href = 'key=update_events';

        return '<a href="'.$this->addToUrl($href).'" title="'.StringUtil::specialchars($title).'" class="'.$class.'"'.$attributes.'>'.$label.'</a> ';
    }
}
