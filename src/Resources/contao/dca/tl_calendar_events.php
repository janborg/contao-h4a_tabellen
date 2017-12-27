<?php


/*
 * Extend palettes
 */

 \Contao\CoreBundle\DataContainer\PaletteManipulator::create()
->addLegend('h4a_legend', 'title_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_AFTER)
->addField('gGameNo,gClassName,gHomeTeam,gGuestTeam,gResult,gHalfResult', 'h4a_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
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
        array('gResult' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gResult'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql' => "varchar(255) NOT NULL default ''",
        ),
        ),
        array('gHalfResult' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gHalfResult'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql' => "varchar(255) NOT NULL default ''",
        )),
        $GLOBALS['TL_DCA']['tl_calendar_events']['fields']
    );

