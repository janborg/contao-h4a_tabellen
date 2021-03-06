<?php

/*
 * This file is part of contao-h4a_tabellen.
 * (c) Jan Lünborg
 * @license MIT
 */

namespace Janborg\H4aTabellen\Cron;

use Contao\Backend;
use Contao\CalendarEventsModel;
use Contao\CalendarModel;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\Input;
use Contao\StringUtil;
use Contao\System;
use Janborg\H4aTabellen\Helper\Helper;
use Psr\Log\LogLevel;

/**
 * Class H4aEventAutomator.
 */
class H4aEventAutomator extends Backend
{
    public function __construct()
    {
        parent::__construct();
    }

    public function updateEvents()
    {
        $objCalendars = CalendarModel::findby(
            ['tl_calendar.h4a_imported=?', 'tl_calendar.h4a_ignore !=?'],
            ['1', '1']
        );

        $intCalendars = \count($objCalendars);

        System::getContainer()
        ->get('monolog.logger.contao')
        ->log(LogLevel::INFO, 'Update für '.$intCalendars.' Kalender über Handball4all gestartet', ['contao' => new ContaoContext(__CLASS__.'::'.__FUNCTION__, TL_GENERAL)]);

        foreach ($objCalendars as $objCalendar) {
            $this->syncCalendars($objCalendar);
        }

        System::getContainer()
        ->get('monolog.logger.contao')
        ->log(LogLevel::INFO, 'Update der Kalender über Handball4all beendet', ['contao' => new ContaoContext(__CLASS__.'::'.__FUNCTION__, TL_GENERAL)]);

        $this->redirect($this->getReferer());
    }

    public function updateArchive()
    {
        $id = [Input::get('id')];

        $objCalendar = CalendarModel::findById($id);

        $this->syncCalendars($objCalendar);

        System::getContainer()
        ->get('monolog.logger.contao')
        ->log(LogLevel::INFO, 'Update des Kalenders "'.$objCalendar->title.'" (ID: '.$objCalendar->id.') über Handball4all durchgeführt.', ['contao' => new ContaoContext(__CLASS__.'::'.__FUNCTION__, TL_GENERAL)]);

        $this->redirect($this->getReferer());
    }

    /**
     * Update Calendars via json from H4a.
     */
    public function syncCalendars(CalendarModel $objCalendar)
    {
        $arrResultSpielplan = Helper::getJsonSpielplan($objCalendar->h4a_team_ID);
        $arrResultTabelle = Helper::getJsonTabelle($arrResultSpielplan['dataList'][0]['gClassID']);
        Helper::updateDatabaseFromJsonFile($arrResultSpielplan, $arrResultTabelle);

        if ('/ [error]' === $arrResultSpielplan['lvTypeLabelStr']) {
            System::getContainer()
            ->get('monolog.logger.contao')
            ->log(LogLevel::INFO, 'Updateversuch des Kalenders "'.$objCalendar->title.'" (ID: '.$objCalendar->id.') abgebrochen, prüfen Sie die Team ID!', ['contao' => new ContaoContext(__CLASS__.'::'.__FUNCTION__, TL_GENERAL)]);
        } else {
            $arrSpiele = $arrResultSpielplan['dataList'];

            //Update or Create Event
            foreach ($arrSpiele as $arrSpiel) {
                $objEvent = CalendarEventsModel::findOneBy(
                    ['gGameNo=?', 'pid=?'],
                    [$arrSpiel['gNo'], $objCalendar->id]
                );

                //Update, wenn ModelObjekt existiert
                if (null !== $objEvent) {
                    $arrDate = explode('.', $arrSpiel['gDate']);
                    if (!preg_match('/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/', $arrSpiel['gTime'])) {
                        $arrSpiel['gTime'] = '00:00';
                    }
                    $arrTime = explode(':', $arrSpiel['gTime']);

                    $dateDay = mktime(0, 0, 0, $arrDate[1], $arrDate[0], $arrDate[2]);
                    $dateTime = mktime($arrTime[0], $arrTime[1], 0, $arrDate[1], $arrDate[0], $arrDate[2]);

                    $objEvent->gGameID = $arrSpiel['gID'];
                    $objEvent->author = $objCalendar->h4aEvents_author;
                    $objEvent->source = 'default';
                    $objEvent->addTime = 1;
                    $objEvent->startTime = $dateTime;
                    $objEvent->endTime = $dateTime;
                    $objEvent->startDate = $dateDay;
                    $objEvent->gClassName = $arrSpiel['gClassSname'];
                    $objEvent->gHomeTeam = $arrSpiel['gHomeTeam'];
                    $objEvent->gGuestTeam = $arrSpiel['gGuestTeam'];
                    $objEvent->gGymnasiumNo = $arrSpiel['gGymnasiumNo'];
                    $objEvent->gGymnasiumName = $arrSpiel['gGymnasiumName'];
                    $objEvent->location = $arrSpiel['gGymnasiumName'];
                    $objEvent->address = $arrSpiel['gGymnasiumStreet'].', '.$arrSpiel['gGymnasiumPostal'].' '.$arrSpiel['gGymnasiumTown'];
                    $objEvent->gGymnasiumStreet = $arrSpiel['gGymnasiumStreet'];
                    $objEvent->gGymnasiumTown = $arrSpiel['gGymnasiumTown'];
                    $objEvent->gGymnasiumPostal = $arrSpiel['gGymnasiumPostal'];
                    $objEvent->gHomeGoals = $arrSpiel['gHomeGoals'];
                    $objEvent->gGuestGoals = $arrSpiel['gGuestGoals'];
                    $objEvent->gHomeGoals_1 = $arrSpiel['gHomeGoals_1'];
                    $objEvent->gGuestGoals_1 = $arrSpiel['gGuestGoals_1'];
                    $objEvent->gComment = $arrSpiel['gComment'];
                    $objEvent->published = true;

                    if (' ' !== $arrSpiel['gHomeGoals'] && ' ' !== $arrSpiel['gGuestGoals']) {
                        $objEvent->h4a_resultComplete = true;
                    } else {
                        $objEvent->h4a_resultComplete = false;
                    }

                    $objEvent->save();

                //Create Event, wenn ModelObjekt existiert
                } else {
                    $objEvent = new CalendarEventsModel();

                    $arrDate = explode('.', $arrSpiel['gDate']);
                    if (!preg_match('/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/', $arrSpiel['gTime'])) {
                        $arrSpiel['gTime'] = '00:00';
                    }
                    $arrTime = explode(':', $arrSpiel['gTime']);

                    $dateDay = mktime(0, 0, 0, $arrDate[1], $arrDate[0], $arrDate[2]);
                    $dateTime = mktime($arrTime[0], $arrTime[1], 0, $arrDate[1], $arrDate[0], $arrDate[2]);

                    $objEvent->pid = $objCalendar->id;
                    $objEvent->timestamp = time();
                    $objEvent->title = $arrSpiel['gClassSname'].': '.$arrSpiel['gHomeTeam'].' - '.$arrSpiel['gGuestTeam'];
                    $objEvent->alias = StringUtil::generateAlias($arrSpiel['gClassSname'].'_'.$arrSpiel['gHomeTeam'].'_'.$arrSpiel['gGuestTeam'].'_'.$arrSpiel['gNo']);
                    $objEvent->gGameID = $arrSpiel['gID'];
                    $objEvent->gGameNo = $arrSpiel['gNo'];
                    $objEvent->gClassName = $arrSpiel['gClassSname'];
                    $objEvent->gHomeTeam = $arrSpiel['gHomeTeam'];
                    $objEvent->gGuestTeam = $arrSpiel['gGuestTeam'];

                    $objEvent->author = $objCalendar->h4aEvents_author;
                    $objEvent->source = 'default';
                    $objEvent->addTime = 1;
                    $objEvent->startTime = $dateTime;
                    $objEvent->endTime = $dateTime;
                    $objEvent->startDate = $dateDay;
                    $objEvent->gGymnasiumNo = $arrSpiel['gGymnasiumNo'];
                    $objEvent->gGymnasiumName = $arrSpiel['gGymnasiumName'];
                    $objEvent->location = $arrSpiel['gGymnasiumName'];
                    $objEvent->address = $arrSpiel['gGymnasiumStreet'].', '.$arrSpiel['gGymnasiumPostal'].' '.$arrSpiel['gGymnasiumTown'];
                    $objEvent->gGymnasiumStreet = $arrSpiel['gGymnasiumStreet'];
                    $objEvent->gGymnasiumTown = $arrSpiel['gGymnasiumTown'];
                    $objEvent->gGymnasiumPostal = $arrSpiel['gGymnasiumPostal'];
                    $objEvent->gHomeGoals = $arrSpiel['gHomeGoals'];
                    $objEvent->gGuestGoals = $arrSpiel['gGuestGoals'];
                    $objEvent->gHomeGoals_1 = $arrSpiel['gHomeGoals_1'];
                    $objEvent->gGuestGoals_1 = $arrSpiel['gGuestGoals_1'];
                    $objEvent->gComment = $arrSpiel['gComment'];
                    $objEvent->published = true;

                    if (' ' !== $arrSpiel['gHomeGoals'] && ' ' !== $arrSpiel['gGuestGoals']) {
                        $objEvent->h4a_resultComplete = true;
                    } else {
                        $objEvent->h4a_resultComplete = false;
                    }

                    $objEvent->save();
                }
            }
        }
    }

    public function updateResults()
    {
        $objEvents = CalendarEventsModel::findby(
            ['DATE(FROM_UNIXTIME(startDate)) = ?', 'h4a_resultComplete != ?'],
            [date('Y-m-d'), true]
        );

        if (null === $objEvents) {
            $this->redirect($this->getReferer());

            return;
        }
        foreach ($objEvents as $objEvent) {
            if ($objEvent->startTime > time() || '00:00' === date('H:i', $objEvent->startTime)) {
                continue;
            }

            $objCalendar = CalendarModel::findById($objEvent->pid);

            $arrResult = Helper::getJsonSpielplan($objCalendar->h4a_team_ID);

            $games = $arrResult['dataList'];
            $gameId = array_search($objEvent->gGameNo, array_column($games, 'gNo'), true);

            if (' ' !== $games[$gameId]['gHomeGoals'] && ' ' !== $games[$gameId]['gGuestGoals']) {
                $objEvent->gHomeGoals = $games[$gameId]['gHomeGoals'];
                $objEvent->gGuestGoals = $games[$gameId]['gGuestGoals'];
                $objEvent->gHomeGoals_1 = $games[$gameId]['gHomeGoals_1'];
                $objEvent->gGuestGoals_1 = $games[$gameId]['gGuestGoals_1'];
                $objEvent->h4a_resultComplete = true;
                $objEvent->save();

                System::getContainer()
                ->get('monolog.logger.contao')
                ->log(LogLevel::INFO, 'Ergebnis ('.$games[$gameId]['gHomeGoals'].':'.$games[$gameId]['gGuestGoals'].') für Spiel '.$objEvent->gGameNo.' über Handball4all aktualisiert', ['contao' => new ContaoContext(__CLASS__.'::'.__FUNCTION__, TL_GENERAL)]);
            } else {
                $objEvent->h4a_resultComplete = false;

                System::getContainer()
                ->get('monolog.logger.contao')
                ->log(LogLevel::INFO, 'Ergebnis für Spiel '.$objEvent->gGameNo.' über Handball4all geprüft, kein Ergebnis vorhanden', ['contao' => new ContaoContext(__CLASS__.'::'.__FUNCTION__, TL_GENERAL)]);
            }
        }
        $this->redirect($this->getReferer());
    }
}
