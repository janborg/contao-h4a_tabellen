<?php


/*
 * Extend palettes
 */

 \Contao\CoreBundle\DataContainer\PaletteManipulator::create()
	->addLegend('h4a_legend', 'title_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_AFTER)
	->addField('gGameNo,gClassName,gHomeTeam,gGuestTeam', 'h4a_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
	->applyToPalette('default', 'tl_calendar_events');

 \Contao\CoreBundle\DataContainer\PaletteManipulator::create()
	->addLegend('gymnasium_legend', 'h4a_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_AFTER)
	->addField('gGymnasiumNo,gGymnasiumName,gGymnasiumStreet,gGymnasiumTown,gGymnasiumPostal', 'gymnasium_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
	->applyToPalette('default', 'tl_calendar_events');

 \Contao\CoreBundle\DataContainer\PaletteManipulator::create()
	->addLegend('result_legend', 'gymnasium_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_AFTER)
	->addField('gHomeGoals,gGuestGoals,gHomeGoals_1,gGuestGoals_1', 'result_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
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


/*
 * Table tl_calendar_events
 */

 $GLOBALS['TL_DCA']['tl_calendar_events']['fields'] = array_merge(
        array('gGameNo' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gGameNo'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql' => "varchar(255) NOT NULL default ''",
        )),
        array('gClassName' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gClassName'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql' => "varchar(255) NOT NULL default ''",
        )),
        array('gHomeTeam' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gHomeTeam'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql' => "varchar(255) NOT NULL default ''",
        )),
        array('gGuestTeam' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gGuestTeam'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql' => "varchar(255) NOT NULL default ''",
        )),
		array('gGymnasiumNo' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gGymnasiumNo'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => array('mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql' => "varchar(255) NOT NULL default ''",
        )),
		array('gGymnasiumName' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gGymnasiumName'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => array('mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql' => "varchar(255) NOT NULL default ''",
        )),
		array('gGymnasiumStreet' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gGymnasiumStreet'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => array('mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql' => "varchar(255) NOT NULL default ''",
        )),
		array('gGymnasiumTown' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gGymnasiumTown'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => array('mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql' => "varchar(255) NOT NULL default ''",
        )),
		array('gGymnasiumPostal' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gGymnasiumPostal'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => array('mandatory' => false, 'maxlength' => 5, 'tl_class' => 'w50'),
            'sql' => "varchar(255) NOT NULL default ''",
        )),
        array('gHomeGoals' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gHomeGoals'],
            'exclude' => true,
            'search' => false,
            'inputType' => 'text',
            'eval' => array('mandatory' => false, 'maxlength' => 3, 'rgxp' => 'digit', 'tl_class' => 'w50'),
            'sql' => "varchar(255) NOT NULL default ''",
        )),
        array('gGuestGoals' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gGuestGoals'],
            'exclude' => true,
            'search' => false,
            'inputType' => 'text',
            'eval' => array('mandatory' => false, 'maxlength' => 3, 'rgxp' => 'digit', 'tl_class' => 'w50'),
            'sql' => "varchar(255) NOT NULL default ''",
        )),
		array('gHomeGoals_1' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gHomeGoals_1'],
            'exclude' => true,
            'search' => false,
            'inputType' => 'text',
            'eval' => array('mandatory' => false, 'maxlength' => 3, 'rgxp' => 'digit', 'tl_class' => 'w50'),
            'sql' => "varchar(255) NOT NULL default ''",
        )),
        array('gGuestGoals_1' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gGuestGoals_1'],
            'exclude' => true,
            'search' => false,
            'inputType' => 'text',
            'eval' => array('mandatory' => false, 'maxlength' => 3, 'rgxp' => 'digit', 'tl_class' => 'w50'),
            'sql' => "varchar(255) NOT NULL default ''",
        )),
        $GLOBALS['TL_DCA']['tl_calendar_events']['fields']
    );

