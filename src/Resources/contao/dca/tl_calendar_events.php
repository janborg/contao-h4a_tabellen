<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

use Contao\Calendar;
use Contao\CalendarEventsModel;
use Contao\CalendarModel;
use Contao\Config;
use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\Date;
use Contao\Input;
use Contao\StringUtil;

/*
 * amend onload_callback
 */
$GLOBALS['TL_DCA']['tl_calendar_events']['config']['onload_callback'] = array_merge(
    [
        ['tl_calendar_events_h4a', 'h4a_event_imported'],
    ],
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
    ['h4a_update' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['operationImportFromH4a'],
        'class' => 'header_h4a',
        'href' => 'key=update_calendar',
        'icon' => 'bundles/janborgh4atabellen/update.svg',
        'button_callback' => ['tl_calendar_events_h4a', 'h4a_update'],
    ]],
    $GLOBALS['TL_DCA']['tl_calendar_events']['list']['global_operations']
);

/*
 *Child record callback
 */
$GLOBALS['TL_DCA']['tl_calendar_events']['list']['sorting']['child_record_callback'] = ['tl_calendar_events_h4a', 'listEvents'];

/*
 * Table tl_calendar_events
 */

$GLOBALS['TL_DCA']['tl_calendar_events']['fields'] = array_merge(
    ['gGameID' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gGameID'],
        'exclude' => true,
        'search' => true,
        'inputType' => 'text',
        'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''",
    ]],
    ['gGameNo' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gGameNo'],
        'exclude' => true,
        'search' => true,
        'inputType' => 'text',
        'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''",
    ]],
    ['gClassID' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gClassID'],
        'exclude' => true,
        'search' => true,
        'inputType' => 'text',
        'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''",
    ]],
    ['gClassName' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gClassName'],
        'exclude' => true,
        'search' => true,
        'inputType' => 'text',
        'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''",
    ]],
    ['gHomeTeam' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gHomeTeam'],
        'exclude' => true,
        'search' => true,
        'inputType' => 'text',
        'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''",
    ]],
    ['gGuestTeam' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gGuestTeam'],
        'exclude' => true,
        'search' => true,
        'inputType' => 'text',
        'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''",
    ]],
    ['gGymnasiumNo' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gGymnasiumNo'],
        'exclude' => true,
        'search' => true,
        'inputType' => 'text',
        'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
        'sql' => "varchar(255) NULL default ''",
    ]],
    ['gGymnasiumName' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gGymnasiumName'],
        'exclude' => true,
        'search' => true,
        'inputType' => 'text',
        'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
        'sql' => "varchar(255) NULL default ''",
    ]],
    ['gGymnasiumStreet' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gGymnasiumStreet'],
        'exclude' => true,
        'search' => true,
        'inputType' => 'text',
        'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
        'sql' => "varchar(255) NULL default ''",
    ]],
    ['gGymnasiumTown' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gGymnasiumTown'],
        'exclude' => true,
        'search' => true,
        'inputType' => 'text',
        'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
        'sql' => "varchar(255) NULL default ''",
    ]],
    ['gGymnasiumPostal' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gGymnasiumPostal'],
        'exclude' => true,
        'search' => true,
        'inputType' => 'text',
        'eval' => ['mandatory' => false, 'maxlength' => 5, 'tl_class' => 'w50'],
        'sql' => "varchar(255) NULL default ''",
    ]],
    ['gHomeGoals' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gHomeGoals'],
        'exclude' => true,
        'search' => false,
        'inputType' => 'text',
        'eval' => ['mandatory' => false, 'maxlength' => 3, 'rgxp' => 'digit', 'tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''",
    ]],
    ['gGuestGoals' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gGuestGoals'],
        'exclude' => true,
        'search' => false,
        'inputType' => 'text',
        'eval' => ['mandatory' => false, 'maxlength' => 3, 'rgxp' => 'digit', 'tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''",
    ]],
    ['gHomeGoals_1' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gHomeGoals_1'],
        'exclude' => true,
        'search' => false,
        'inputType' => 'text',
        'eval' => ['mandatory' => false, 'maxlength' => 3, 'rgxp' => 'digit', 'tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''",
    ]],
    ['gGuestGoals_1' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gGuestGoals_1'],
        'exclude' => true,
        'search' => false,
        'inputType' => 'text',
        'eval' => ['mandatory' => false, 'maxlength' => 3, 'rgxp' => 'digit', 'tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''",
    ]],
    ['h4a_resultComplete' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['h4a_resultComplete'],
        'exclude' => true,
        'filter' => true,
        'inputType' => 'checkbox',
        'eval' => ['tl_class' => 'w50 m12'],
        'sql' => "char(1) NOT NULL default ''",
    ]],
    ['gComment' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['gComment'],
        'exclude' => true,
        'search' => false,
        'inputType' => 'text',
        'eval' => ['mandatory' => false, 'tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''",
    ]],
    ['sGID' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['sGID'],
        'exclude' => true,
        'search' => true,
        'inputType' => 'text',
        'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''",
    ]],
    ['h4a_season' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar']['h4a_season'],
        'inputType' => 'select',
        'foreignKey' => 'tl_h4a_seasons.season',
        'filter' => true,
        'exclude' => true,
        'eval' => [
            'mandatory' => true,
            'unique' => false,
            'tl_class' => 'w50',
        ],
        'sql' => "varchar(9) NOT NULL default ''",
    ]],
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
        public function h4a_event_imported(DataContainer $dc): void
        {
            $objCalendarEvent = CalendarEventsModel::findById(Input::get('id'));
            $objCalendar = CalendarModel::findById($objCalendarEvent->pid);

            if ('1' === $objCalendar->h4a_imported) {
                PaletteManipulator::create()
                    ->addLegend('h4a_legend', 'title_legend', PaletteManipulator::POSITION_AFTER)
                    ->addField('gHomeTeam,gGuestTeam,gClassID,gClassName,h4a_season,gGameNo', 'h4a_legend', PaletteManipulator::POSITION_APPEND)
                    ->applyToPalette('default', 'tl_calendar_events')
                ;

                PaletteManipulator::create()
                    ->addLegend('gymnasium_legend', 'h4a_legend', PaletteManipulator::POSITION_AFTER)
                    ->addField('gGymnasiumNo,gGymnasiumName,gGymnasiumStreet,gGymnasiumTown,gGymnasiumPostal', 'gymnasium_legend', PaletteManipulator::POSITION_APPEND)
                    ->applyToPalette('default', 'tl_calendar_events')
                ;

                PaletteManipulator::create()
                    ->addLegend('result_legend', 'gymnasium_legend', PaletteManipulator::POSITION_AFTER)
                    ->addField('gHomeGoals,gGuestGoals,gHomeGoals_1,gGuestGoals_1,sGID,gComment,h4a_resultComplete', 'result_legend', PaletteManipulator::POSITION_APPEND)
                    ->applyToPalette('default', 'tl_calendar_events')
                ;
            }
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
            $id = strlen(Input::get('id')) ? Input::get('id') : CURRENT_ID;

            return '<a href="'.$this->addToUrl($href.'&amp;id='.$id).'" title="'.StringUtil::specialchars($title).'" class="'.$class.'"'.$attributes.'>'.$label.'</a> ';
        }

        /**
         * Add the type of input field.
         *
         * @param array $arrRow
         *
         * @return string
         */
        public function listEvents($arrRow)
        {
            $objCalendar = CalendarModel::findById(Input::get('id'));

            $span = Calendar::calculateSpan($arrRow['startTime'], $arrRow['endTime']);

            if ($span > 0) {
                $date = Date::parse(Config::get(($arrRow['addTime'] ? 'datimFormat' : 'dateFormat')), $arrRow['startTime']).$GLOBALS['TL_LANG']['MSC']['cal_timeSeparator'].Date::parse(Config::get(($arrRow['addTime'] ? 'datimFormat' : 'dateFormat')), $arrRow['endTime']);
            } elseif ($arrRow['startTime'] === $arrRow['endTime']) {
                $date = Date::parse(Config::get('dateFormat'), $arrRow['startTime']).($arrRow['addTime'] ? ' '.Date::parse(Config::get('timeFormat'), $arrRow['startTime']) : '');
            } else {
                $date = Date::parse(Config::get('dateFormat'), $arrRow['startTime']).($arrRow['addTime'] ? ' '.Date::parse(Config::get('timeFormat'), $arrRow['startTime']).$GLOBALS['TL_LANG']['MSC']['cal_timeSeparator'].Date::parse(Config::get('timeFormat'), $arrRow['endTime']) : '');
            }

            $result = ' ';
            // Show result in listview only, when existing
            if ('1' === $arrRow['h4a_resultComplete']) {
                $result = $arrRow['gHomeGoals'].' : '.$arrRow['gGuestGoals'].' ('.$arrRow['gHomeGoals_1'].' : '.$arrRow['gGuestGoals_1'].')';
            }

            //different listview with result for calendars, that are updated via h4a
            if ('1' === $objCalendar->h4a_imported) {
                return '<div class="tl_content_left"><span style="padding-right:3px">['.$date.']</span>'.$arrRow['title'].' <span style="color:#999;padding-left:3px">'.$result.'</span> </div>';
            }

            return '<div class="tl_content_left">'.$arrRow['title'].' <span style="color:#999;padding-left:3px">['.$date.']</span></div>';
        }
    }
