<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

namespace Janborg\H4aTabellen\H4aEventAutomator;

use Contao\Backend;
use Contao\CalendarEventsModel;
use Contao\CalendarModel;
use Contao\CoreBundle\Cache\EntityCacheTags;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Input;
use Contao\StringUtil;
use Contao\System;
use Janborg\H4aTabellen\Helper\Helper;
use Janborg\H4aTabellen\Model\H4aSeasonModel;

/**
 * Class H4aEventAutomator.
 */
class H4aEventAutomator extends Backend
{
    public function __construct(
        private ContaoFramework $contaoFramework,
        private EntityCacheTags $entityCacheTags,
    ) {
        $this->contaoFramework->initialize();
        parent::__construct();
    }

    public function updateEvents(): void
    {
        $objCalendars = CalendarModel::findby(
            ['tl_calendar.h4a_imported=?', 'tl_calendar.h4a_ignore !=?'],
            ['1', '1'],
        );

        $intCalendars = \count($objCalendars);

        System::getContainer()
            ->get('monolog.logger.contao.general')
            ->info('Update für '.$intCalendars.' Kalender über Handball4all gestartet')
        ;

        foreach ($objCalendars as $objCalendar) {
            $this->syncCalendars($objCalendar);
        }

        System::getContainer()
            ->get('monolog.logger.contao.general')
            ->info('Update der Kalender über Handball4all beendet')
        ;

        $this->redirect($this->getReferer());
    }

    public function updateArchive(): void
    {
        $id = [Input::get('id')];

        $objCalendar = CalendarModel::findById($id);

        $this->syncCalendars($objCalendar);

        System::getContainer()
            ->get('monolog.logger.contao.general')
            ->info('Update des Kalenders "'.$objCalendar->title.'" (ID: '.$objCalendar->id.') über Handball4all durchgeführt.')
        ;

        $this->redirect($this->getReferer());
    }

    /**
     * Update Calendars via json from H4a.
     */
    public function syncCalendars(CalendarModel $objCalendar): void
    {
        $arrSeasons = unserialize($objCalendar->h4a_seasons);

        foreach ($arrSeasons as $arrSeason) {
            $seasonID = H4aSeasonModel::findById($arrSeason['h4a_saison'])->id;

            $arrResultSpielplan = Helper::getJsonSpielplan($arrSeason['h4a_team']);
            $arrResultTabelle = Helper::getJsonTabelle($arrResultSpielplan['dataList'][0]['gClassID']);
            Helper::updateDatabaseFromJsonFile($arrResultSpielplan, $arrResultTabelle);

            if ('/ [error]' === $arrResultSpielplan['lvTypeLabelStr']) {
                System::getContainer()
                    ->get('monolog.logger.contao.general')
                    ->info('Updateversuch des Kalenders "'.$objCalendar->title.'" (ID: '.$objCalendar->id.') abgebrochen, prüfen Sie die Team ID!')
                ;
            } else {
                $arrSpiele = $arrResultSpielplan['dataList'];

                // Update or Create Event
                foreach ($arrSpiele as $arrSpiel) {
                    $objEvent = CalendarEventsModel::findOneBy(
                        ['gGameNo=?', 'pid=?', 'gClassID=?', 'gGameID=?'],
                        [$arrSpiel['gNo'], $objCalendar->id, $arrSeason['h4a_liga'], $arrSpiel['gID']], );

                    // Update, wenn ModelObjekt existiert
                    if (null !== $objEvent) {
                        $isChanged = false;

                        $arrDate = explode('.', $arrSpiel['gDate']);

                        if (!isset($arrDate[0]) || !isset($arrDate[1]) || !isset($arrDate[2])) {
                            continue;
                        }

                        if (!preg_match('/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/', $arrSpiel['gTime'])) {
                            $arrSpiel['gTime'] = '00:00';
                        }
                        $arrTime = explode(':', $arrSpiel['gTime']);

                        $dateDay = mktime(0, 0, 0, (int) $arrDate[1], (int) $arrDate[0], (int) $arrDate[2]);
                        $dateTime = mktime((int) $arrTime[0], (int) $arrTime[1], 0, (int) $arrDate[1], (int) $arrDate[0], (int) $arrDate[2]);

                        $objEvent->h4a_season = $seasonID;
                        $objEvent->author = $objCalendar->h4aEvents_author;
                        $objEvent->source = 'default';
                        $objEvent->addTime = true;

                        // Check, if class ID or name changed
                        if (
                            $arrSpiel['gClassID'] !== $objEvent->gClassID
                            || $arrSpiel['gClassSname'] !== $objEvent->gClassName
                        ) {
                            $objEvent->gClassName = $arrSpiel['gClassSname'];
                            $objEvent->gClassID = $arrSpiel['gClassID'];
                            $isChanged = true;
                        }

                        // Check, if startTime changed
                        if ($dateTime !== $objEvent->startTime) {
                            $objEvent->startTime = $dateTime;
                            $objEvent->endTime = $dateTime + 5400;
                            $isChanged = true;
                        }

                        // Check, if Day changed
                        if ($dateDay !== $objEvent->startDate) {
                            $objEvent->startDate = $dateDay;
                            $isChanged = true;
                        }

                        // Check, if Teams changed
                        if (
                            $objEvent->gHomeTeam !== $arrSpiel['gHomeTeam']
                            || $objEvent->gGuestTeam !== $arrSpiel['gGuestTeam']
                        ) {
                            $objEvent->gHomeTeam = $arrSpiel['gHomeTeam'];
                            $objEvent->gGuestTeam = $arrSpiel['gGuestTeam'];
                            $isChanged = true;
                        }

                        // Check, if gGymnasiumNo changed
                        if ($objEvent->gGymnasiumNo !== $arrSpiel['gGymnasiumNo']) {
                            $objEvent->gGymnasiumNo = $arrSpiel['gGymnasiumNo'];
                            $objEvent->gGymnasiumName = $arrSpiel['gGymnasiumName'];
                            $objEvent->location = $arrSpiel['gGymnasiumName'];
                            $objEvent->address = $arrSpiel['gGymnasiumStreet'].', '.$arrSpiel['gGymnasiumPostal'].' '.$arrSpiel['gGymnasiumTown'];
                            $objEvent->gGymnasiumStreet = $arrSpiel['gGymnasiumStreet'];
                            $objEvent->gGymnasiumTown = $arrSpiel['gGymnasiumTown'];
                            $objEvent->gGymnasiumPostal = $arrSpiel['gGymnasiumPostal'];
                            $isChanged = true;
                        }

                        // Check, if gComment changed
                        if ($objEvent->gComment !== $arrSpiel['gComment']) {
                            $objEvent->gComment = $arrSpiel['gComment'];
                            $isChanged = true;
                        }

                        // Check, if result changed
                        if (
                            $objEvent->gHomeGoals !== $arrSpiel['gHomeGoals']
                            || $objEvent->gGuestGoals !== $arrSpiel['gGuestGoals']
                            || $objEvent->gHomeGoals_1 !== $arrSpiel['gHomeGoals_1']
                            || $objEvent->gGuestGoals_1 !== $arrSpiel['gGuestGoals_1']
                        ) {
                            $objEvent->gHomeGoals = $arrSpiel['gHomeGoals'];
                            $objEvent->gGuestGoals = $arrSpiel['gGuestGoals'];
                            $objEvent->gHomeGoals_1 = $arrSpiel['gHomeGoals_1'];
                            $objEvent->gGuestGoals_1 = $arrSpiel['gGuestGoals_1'];
                            $isChanged = true;
                        }

                        if (' ' !== $arrSpiel['gHomeGoals'] && ' ' !== $arrSpiel['gGuestGoals']) {
                            $objEvent->h4a_resultComplete = true;
                        } else {
                            $objEvent->h4a_resultComplete = false;
                        }

                        if (true === $isChanged) {
                            // save Event
                            $objEvent->save();

                            // log, that event was changed
                            System::getContainer()
                                ->get('monolog.logger.contao.general')
                                ->info('Event für Spiel '.$arrSpiel['gClassSname'].': '.$arrSpiel['gHomeTeam'].': '.$arrSpiel['gGuestTeam'].' (gID: '.$objEvent->gGameID.') über Handball4all aktualisiert')
                            ;

                            // Invalidate CacheTag for Event
                            $this->entityCacheTags->invalidateTagsFor($objEvent);
                        }

                        // Create Event, wenn ModelObjekt existiert
                    } else {
                        $objEvent = new CalendarEventsModel();

                        $arrDate = explode('.', $arrSpiel['gDate']);

                        if (!isset($arrDate[0]) || !isset($arrDate[1]) || !isset($arrDate[2])) {
                            continue;
                        }

                        if (!preg_match('/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/', $arrSpiel['gTime'])) {
                            $arrSpiel['gTime'] = '00:00';
                        }
                        $arrTime = explode(':', $arrSpiel['gTime']);

                        $dateDay = mktime(0, 0, 0, (int) $arrDate[1], (int) $arrDate[0], (int) $arrDate[2]);
                        $dateTime = mktime((int) $arrTime[0], (int) $arrTime[1], 0, (int) $arrDate[1], (int) $arrDate[0], (int) $arrDate[2]);

                        $objEvent->pid = $objCalendar->id;
                        $objEvent->tstamp = time();
                        $objEvent->title = $arrSpiel['gClassSname'].': '.$arrSpiel['gHomeTeam'].' - '.$arrSpiel['gGuestTeam'];
                        $objEvent->alias = StringUtil::generateAlias($arrSpiel['gClassSname'].'_'.$arrSpiel['gHomeTeam'].'_'.$arrSpiel['gGuestTeam'].'_'.$arrSpiel['gID']);
                        $objEvent->h4a_season = $seasonID;
                        $objEvent->gGameID = $arrSpiel['gID'];
                        $objEvent->gGameNo = $arrSpiel['gNo'];
                        $objEvent->gClassID = $arrSpiel['gClassID'];
                        $objEvent->gClassName = $arrSpiel['gClassSname'];
                        $objEvent->gHomeTeam = $arrSpiel['gHomeTeam'];
                        $objEvent->gGuestTeam = $arrSpiel['gGuestTeam'];

                        $objEvent->author = $objCalendar->h4aEvents_author;
                        $objEvent->source = 'default';
                        $objEvent->addTime = true;
                        $objEvent->startTime = $dateTime;
                        $objEvent->endTime = $dateTime + 5400;
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

                        // save new Event
                        $objEvent->save();

                        // Invalidate CacheTag for Event
                        $this->entityCacheTags->invalidateTagsFor($objEvent);
                    }
                }
            }
        }
    }

    public function updateResults(): void
    {
        $objEvents = CalendarEventsModel::findby(
            ['DATE(FROM_UNIXTIME(startDate)) = ?', 'h4a_resultComplete != ?'],
            [date('Y-m-d'), true],
        );

        if (null === $objEvents) {
            $this->redirect($this->getReferer());

            return; // @phpstan-ignore deadCode.unreachable
        }

        foreach ($objEvents as $objEvent) {
            if ($objEvent->startTime > time() || '00:00' === date('H:i', (int) $objEvent->startTime)) {
                continue;
            }

            $arrResult = Helper::getJsonLigaSpielplan($objEvent->gClassID);

            $games = $arrResult['dataList'];

            if (isset($games[0])) {
                $gameId = array_search($objEvent->gGameID, array_column($games, 'gID'), true);
            } else {
                continue;
            }

            if (' ' !== $games[$gameId]['gHomeGoals'] && ' ' !== $games[$gameId]['gGuestGoals']) {
                $objEvent->gHomeGoals = $games[$gameId]['gHomeGoals'];
                $objEvent->gGuestGoals = $games[$gameId]['gGuestGoals'];
                $objEvent->gHomeGoals_1 = $games[$gameId]['gHomeGoals_1'];
                $objEvent->gGuestGoals_1 = $games[$gameId]['gGuestGoals_1'];
                $objEvent->h4a_resultComplete = true;
                $objEvent->save();

                System::getContainer()
                    ->get('monolog.logger.contao.general')
                    ->info('Ergebnis ('.$games[$gameId]['gHomeGoals'].':'.$games[$gameId]['gGuestGoals'].') für Spiel '.$objEvent->gGameID.' über Handball4all aktualisiert')
                ;

                $this->updateReportIdForEvent($objEvent);
            } else {
                $objEvent->h4a_resultComplete = false;

                System::getContainer()
                    ->get('monolog.logger.contao.general')
                    ->info('Ergebnis für Spiel '.$objEvent->gGameID.' über Handball4all geprüft, kein Ergebnis vorhanden')
                ;
            }
        }
        $this->redirect($this->getReferer());
    }

    /**
     * Update field sGID for all calendarEvents of today or earlier, where sGID is
     * empty and h4a_resultComplete is true.
     */
    public function updateReportIDs(): void
    {
        $objEvents = CalendarEventsModel::findBy(
            ['DATE(FROM_UNIXTIME(startDate)) <= ?', 'sGID = ?', 'h4a_resultComplete = ?'],
            [date('Y-m-d'), '', true],
        );

        if (null === $objEvents) {
            $this->redirect($this->getReferer());

            return; // @phpstan-ignore deadCode.unreachable
        }

        foreach ($objEvents as $objEvent) {
            $this->updateReportIdForEvent($objEvent);
        }
    }

    /**
     * Update field sGID for a single calendarEvent.
     */
    public function updateReportIdForEvent(CalendarEventsModel $objEvent): void
    {
        $sGID = Helper::getReportNo($objEvent->gClassID, $objEvent->gGameNo);

        if (isset($sGID) && null !== $sGID) {
            $objEvent->sGID = $sGID;
            $objEvent->save();

            System::getContainer()
                ->get('monolog.logger.contao.general')
                ->info('Report Nr. '.$objEvent->sGID.' für Spiel '.$objEvent->gGameID.' über Handball4all gespeichert')
            ;
        } else {
            System::getContainer()
                ->get('monolog.logger.contao.general')
                ->info('Report Nr. für Spiel '.$objEvent->title.' ('.$objEvent->gGameID.') konnte nicht ermittelt werden')
            ;
        }
    }
}
