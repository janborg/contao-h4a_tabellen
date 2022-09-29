<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

namespace Janborg\H4aTabellen\Cron;

use Contao\CalendarEventsModel;
use Contao\CalendarModel;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\System;
use Janborg\H4aTabellen\H4aEventAutomator\H4aEventAutomator;
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
            $h4aeventautomator = new H4aEventAutomator();

            $h4aeventautomator->syncCalendars($objCalendar);

            System::getContainer()
                ->get('monolog.logger.contao')
                ->log(LogLevel::DEBUG, 'Update des Kalenders "'.$objCalendar->title.'" (ID: '.$objCalendar->id.') über Handball4all durchgeführt.', ['contao' => new ContaoContext(__CLASS__.'::'.__FUNCTION__, TL_CRON)])
            ;
        }
    }

    public function updateResults(): void
    {
        $objEvents = CalendarEventsModel::findby(
            ['DATE(FROM_UNIXTIME(startDate)) <= ?', 'h4a_resultComplete != ?', 'gGameID != ?'],
            [date('Y-m-d'), true, '']
        );

        foreach ($objEvents as $objEvent) {
            if ($objEvent->startTime > time() || '00:00' === date('H:i', (int) $objEvent->startTime)) {
                continue;
            }

            $objCalendar = CalendarModel::findById($objEvent->pid);

            $h4a_team_ID = Helper::getH4ateamFromH4aSeasons($objCalendar, $objEvent);

            $arrResult = Helper::getJsonSpielplan($h4a_team_ID);

            $games = $arrResult['dataList'];

            if (isset($games[0])) {
                $gameId = array_search($objEvent->gGameID, array_column($games, 'gID'), true);
            } else {
                continue;
            }

            if (
                isset($games[$gameId]['gHomeGoals'], $games[$gameId]['gGuestGoals'])

                && ' ' !== $games[$gameId]['gHomeGoals']
                && ' ' !== $games[$gameId]['gGuestGoals']
            ) {
                $objEvent->gHomeGoals = $games[$gameId]['gHomeGoals'];
                $objEvent->gGuestGoals = $games[$gameId]['gGuestGoals'];
                $objEvent->gHomeGoals_1 = $games[$gameId]['gHomeGoals_1'];
                $objEvent->gGuestGoals_1 = $games[$gameId]['gGuestGoals_1'];
                $objEvent->h4a_resultComplete = true;
                $objEvent->save();

                System::getContainer()
                    ->get('monolog.logger.contao')
                    ->log(LogLevel::DEBUG, 'Ergebnis ('.$games[$gameId]['gHomeGoals'].':'.$games[$gameId]['gGuestGoals'].') für Spiel '.$objEvent->gGameID.' über Handball4all aktualisiert', ['contao' => new ContaoContext(__CLASS__.'::'.__FUNCTION__, TL_CRON)])
                ;
            } else {
                $objEvent->h4a_resultComplete = false;

                System::getContainer()
                    ->get('monolog.logger.contao')
                    ->log(LogLevel::DEBUG, 'Ergebnis für Spiel '.$objEvent->gGameID.' über Handball4all geprüft, kein Ergebnis vorhanden', ['contao' => new ContaoContext(__CLASS__.'::'.__FUNCTION__, TL_CRON)])
                ;
            }
        }
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

            if (isset($sGID) && null !== $sGID) {
                $objEvent->sGID = $sGID;
                $objEvent->save();

                System::getContainer()
                    ->get('monolog.logger.contao')
                    ->log(LogLevel::DEBUG, 'Report Nr. '.$objEvent->sGID.' für Spiel '.$objEvent->title.' ('.$objEvent->gGameID.') über Handball4all gespeichert', ['contao' => new ContaoContext(__CLASS__.'::'.__FUNCTION__, TL_CRON)])
                ;
            } else {
                System::getContainer()
                    ->get('monolog.logger.contao')
                    ->log(LogLevel::DEBUG, 'Report Nr. für Spiel '.$objEvent->title.' ('.$objEvent->gGameID.') konnte nicht ermittelt werden', ['contao' => new ContaoContext(__CLASS__.'::'.__FUNCTION__, TL_CRON)])
                ;
            }
        }
    }
}
