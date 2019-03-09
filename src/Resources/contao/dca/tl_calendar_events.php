<?php

// Load language file(s)
System::loadLanguageFile('tl_h4a');

/*
 * amend onload_callback
 */
 $GLOBALS['TL_DCA']['tl_calendar_events']['config']['onload_callback'] = array_merge(
         array(
             array('tl_calendar_events_h4a', 'h4a_event_imported')
         ),
         $GLOBALS['TL_DCA']['tl_calendar_events']['config']['onload_callback']
 );

/*
 * Extend palettes
 */

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
     array('h4a_update' => array(
             'label' => &$GLOBALS['TL_LANG']['tl_h4a']['operationImportFromH4a'],
             'class' => 'header_h4a',
 			'$href' => 'key=update_calendar',
             'icon' 	=> 'bundles/janborgh4atabellen/update.svg',
             'button_callback' => array('tl_calendar_events_h4a', 'h4a_update'),
         )),
     $GLOBALS['TL_DCA']['tl_calendar_events']['list']['global_operations']
  );
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
            'sql' => "varchar(255) NULL default ''",
        )),
		array('gGymnasiumName' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gGymnasiumName'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => array('mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql' => "varchar(255) NULL default ''",
        )),
		array('gGymnasiumStreet' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gGymnasiumStreet'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => array('mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql' => "varchar(255) NULL default ''",
        )),
		array('gGymnasiumTown' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gGymnasiumTown'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => array('mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql' => "varchar(255) NULL default ''",
        )),
		array('gGymnasiumPostal' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gGymnasiumPostal'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => array('mandatory' => false, 'maxlength' => 5, 'tl_class' => 'w50'),
            'sql' => "varchar(255) NULL default ''",
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

    /**
     * Class tl_calendar_events_h4a.
     */
    class tl_calendar_events_h4a extends Backend
    {
        /**
         * tl_calendar__events_h4a constructor.
         */
        public function __construct()
        {
            parent::__construct();
            $this->import('BackendUser', 'User');
        }

        /**
        * @param DataContainer $dc
        *extend palettes only if checkbox "h4a_imported" in parent calendar is set
        */
         public function h4a_event_imported(DataContainer $dc)
         {
             $objCalendarEvent = \CalendarEventsModel::findById(\Input::get('id'));
             $objCalendar = \CalendarModel::findById($objCalendarEvent->pid);

             if ($objCalendar->h4a_imported == '1')
             {
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
             };
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
    		$href = 'key=update_calendar';
            $id = \strlen(Input::get('id')) ? Input::get('id') : CURRENT_ID;

            return '<a href="'.$this->addToUrl($href.'&amp;id='.$id).'" title="'.StringUtil::specialchars($title).'" class="'.$class.'"'.$attributes.'>'.$label.'</a> ';
        }
    }
