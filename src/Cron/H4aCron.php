<?php

namespace Janborg\H4aTabellen\Cron;

use Contao\CalendarEventsModel;
use Contao\CalendarModel;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\System;
use Janborg\H4aTabellen\H4aEventautomator\H4aEventAutomator;
use Janborg\H4aTabellen\Helper\Helper;
use Psr\Log\LogLevel;

class H4aCron
{
    /**
     * @var ContaoFramework
     */
    private $framework;

    public function __construct(ContaoFramework $framework)
    {
        $this->framework = $framework;
        $this->framework->initialize();
    }
    
    public function updateEvents(): void
    {
        $objCalendars = CalendarModel::findby(
            ['tl_calendar.h4a_imported=?', 'tl_calendar.h4a_ignore !=?'],
            ['1', '1']
        );
        
        foreach ($objCalendars as $objCalendar) {

            $h4aeventautomator = new H4aEventAutomator;
            
            $h4aeventautomator->syncCalendars($objCalendar);

            System::getContainer()
                        ->get('monolog.logger.contao')
                        ->log(LogLevel::INFO, 'Update des Kalenders "'.$objCalendar->title.'" (ID: '.$objCalendar->id.') über Handball4all durchgeführt.', ['contao' => new ContaoContext(__CLASS__.'::'.__FUNCTION__, TL_CRON)]);
        }
    }

    public function updateResults(): void
    {
        // Do something …
    }
    public function updateReports(): void
    {
        $objEvents = CalendarEventsModel::findby(
            ['DATE(FROM_UNIXTIME(startDate)) <= ?', 'h4a_resultComplete = ?', 'sGID = ?'],
            [date('Y-m-d'), true, '']
        );

        if (null === $objEvents) {

            return;
        }

        foreach ($objEvents as $objEvent) {

            $sGID = Helper::getReportNo($objEvent->gClassID, $objEvent->gGameNo);
            
            if (isset($sGID)) {
                $objEvent->sGID = $sGID;
                $objEvent->save();
                
                System::getContainer()
                        ->get('monolog.logger.contao')
                        ->log(LogLevel::INFO, 'Report Nr. '.$objEvent->sGID.' für Spiel '.$objEvent->gGameNo.' über Handball4all gespeichert', ['contao' => new ContaoContext(__CLASS__.'::'.__FUNCTION__, TL_CRON)]);
            };
        }

    }
}