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
use Janborg\H4aTabellen\HandballnetApiClient;
use Janborg\H4aTabellen\Model\H4aSeasonModel;
use Psr\Log\LoggerInterface;

/**
 * Class H4aEventAutomator.
 */
class H4aEventAutomator extends Backend
{
    public function __construct(
        private ContaoFramework $contaoFramework,
        private EntityCacheTags $entityCacheTags,
        private HandballnetApiClient $handballnetApiClient,
        private readonly LoggerInterface|null $logger,
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

        $this->logger?->info('Update für '.$intCalendars.' Kalender über Handball4all gestartet');

        foreach ($objCalendars as $objCalendar) {
            $this->syncCalendars($objCalendar, false);
        }

        $this->logger?->info('Update der Kalender über Handball4all beendet');

        $this->redirect($this->getReferer());
    }

    public function updateArchive(): void
    {
        $id = [Input::get('id')];

        $objCalendar = CalendarModel::findById($id);

        $this->syncCalendars($objCalendar, false);

        $this->logger?->info('Update des Kalenders "'.$objCalendar->title.'" (ID: '.$objCalendar->id.') über Handball4all durchgeführt.');

        $this->redirect($this->getReferer());
    }

    /**
     * Update Calendars via json from H4a.
     */
    public function syncCalendars(CalendarModel $objCalendar, bool $cache = true): void
    {
        $arrSeasons = unserialize($objCalendar->h4a_seasons);

        foreach ($arrSeasons as $arrSeason) {
            $seasonID = H4aSeasonModel::findById($arrSeason['h4a_saison'])->id;

            try {
                $data = json_decode($this->handballnetApiClient->getTeamScheduleData($arrSeason['handballnet_id']), true);
            } catch (\Exception $e) {
                $this->logger?->error($e->getMessage());
            }

            // continue if no matches are given
            if (!isset($data['data']) || !\is_array($data['data'])) {
                continue;
            }

            if (isset($data['code']) && '400' === $data['code']) {
                $this->logger?->info('Updateversuch des Kalenders "'.$objCalendar->title.'" (ID: '.$objCalendar->id.') abgebrochen, prüfen Sie die handballnet ID!');
                continue;
            }

            $arrSpiele = $data['data'];

            // Delete events, when sGID does not exist in $arrSpiele
            $objEvents = CalendarEventsModel::findBy(
                ['pid=?', 'h4a_season=?', 'gClassName=?'],
                [$objCalendar->id, $arrSeason['h4a_saison'], $arrSeason['liga_shortname']],
            );

            if (null !== $objEvents) {
                // Wenn Events im Kalender existieren, aber nicht auf h4a, dann nicht mehr
                // existierende Spiele löschen
                foreach ($objEvents as $event) {
                    // prüfen, ob handballnet_id des Events in aktuellem Spielplan existiert
                    // (handball4all.wuerttemberg.1234567)
                    $existingEvent = array_filter(
                        $arrSpiele,
                        static fn ($spiel) => $event->provider.'.'.$event->verband.'.'.$event->gGameID === $spiel['id'],
                    );

                    // wenn nicht, Event löschen
                    if (empty($existingEvent)) {
                        $event->delete();
                        $this->logger?->info('Event '.$event->gClassname.': '.$event->gHomeTeam.': '.$event->gGuestTeam.' (gID: '.$event->gGameID.') wurde gelöscht');
                    }
                    unset($existingEvent);
                }
            }

            // Update or Create Event
            foreach ($arrSpiele as $arrSpiel) {
                $handballnetIdParts = explode('.', $arrSpiel['id']);

                $objEvent = CalendarEventsModel::findOneBy(
                    ['pid=?', 'provider=?', 'verband=?', 'gGameID=?'],
                    [$objCalendar->id, $handballnetIdParts[0], $handballnetIdParts[1], $handballnetIdParts[2]],
                );

                // Update, wenn ModelObjekt existiert
                if (null !== $objEvent) {
                    $isChanged = false;

                    $objEvent->h4a_season = $seasonID;
                    $objEvent->author = $objCalendar->h4aEvents_author;
                    $objEvent->source = 'default';
                    $objEvent->addTime = true;
                    $objEvent->handballnet_id = $arrSpiel['id'] ?? '';

                    // Check, if class ID or name changed
                    if (
                        $arrSeason['liga_shortname'] !== $objEvent->gClassName
                        //|| $arrSeason['liga_name'] !== $objEvent->liga_name
                        || $handballnetIdParts[0] !== $objEvent->provider
                        || $handballnetIdParts[1] !== $objEvent->verband
                    ) {
                        $objEvent->gClassName = $arrSpiel['phase']['acronym'] ?? '';
                        $objEvent->liga_name = $arrSpiel['phase']['name'] ?? '';
                        $objEvent->provider = $handballnetIdParts[0] ?? '';
                        $objEvent->verband = $handballnetIdParts[1] ?? '';
                        $isChanged = true;
                    }

                    // Check, if startTime changed
                    if ($arrSpiel['startsAt'] / 1000 !== $objEvent->startTime) {
                        $objEvent->startTime = $arrSpiel['startsAt'] / 1000;
                        $objEvent->endTime = $arrSpiel['startsAt'] / 1000 + 5400;
                        $isChanged = true;
                    }

                    // Check, if Day changed
                    if ($arrSpiel['startsAt'] / 1000 !== $objEvent->startDate) {
                        $objEvent->startDate = $arrSpiel['startsAt'] / 1000;
                        $isChanged = true;
                    }

                    // Check, if Teams changed
                    if (
                        $objEvent->gHomeTeam !== $arrSpiel['homeTeam']['name']
                        || $objEvent->gGuestTeam !== $arrSpiel['awayTeam']['name']
                    ) {
                        $objEvent->gHomeTeam = $arrSpiel['homeTeam']['name'];
                        $objEvent->gGuestTeam = $arrSpiel['awayTeam']['name'];
                        $isChanged = true;
                    }

                    // Check, if gGymnasiumNo changed
                    if ($objEvent->gGymnasiumNo !== $arrSpiel['field']['fieldNumber']) {
                        $objEvent->gGymnasiumNo = $arrSpiel['field']['fieldNumber'];
                        $objEvent->gGymnasiumName = $arrSpiel['field']['name'];
                        $objEvent->location = $arrSpiel['field']['name'];
                        $objEvent->address = $arrSpiel['field']['city'] ?? ''; // TODO Field Adresseüber Api
                        // $objEvent->gGymnasiumStreet = $arrSpiel['gGymnasiumStreet'];
                        $objEvent->gGymnasiumTown = $arrSpiel['field']['city'];
                        // $objEvent->gGymnasiumPostal = $arrSpiel['gGymnasiumPostal'];
                        $isChanged = true;
                    }

                    // check if reportUrl changed
                    if (null !== $arrSpiel['pdfUrl'] && '' !== $arrSpiel['pdfUrl']) {
                        parse_str(parse_url($arrSpiel['pdfUrl'], PHP_URL_QUERY), $params);
                        if (
                            $objEvent->sGID !== $params['sGID']
                        ) {
                            $objEvent->sGID = $params['sGID'];
                            $isChanged = true;
                        }
                    }

                    // TODO: How to check if it has changed ?!
                    if ('Post' === $arrSpiel['state']) {
                        $objEvent->gHomeGoals = $arrSpiel['homeGoals'];
                        $objEvent->gGuestGoals = $arrSpiel['awayGoals'];
                        $objEvent->gHomeGoals_1 = $arrSpiel['homeGoalsHalf'] ?? '';
                        $objEvent->gGuestGoals_1 = $arrSpiel['awayGoalsHalf'] ?? '';
                        $objEvent->h4a_resultComplete = true;
                    } else {
                        $objEvent->h4a_resultComplete = false;
                    }

                    if (true === $isChanged) {
                        // save Event
                        $objEvent->save();

                        // log, that event was changed
                        $this->logger?->info('Event für Spiel '.$arrSpiel['tournament']['acronym'].': '.$arrSpiel['homeTeam']['name'].': '.$arrSpiel['awayTeam']['name'].' (gID: '.$objEvent->gGameID.') über Handball4all aktualisiert');

                        // Invalidate CacheTag for Event
                        $this->entityCacheTags->invalidateTagsFor($objEvent);
                    }

                    // Create Event, wenn ModelObjekt existiert
                } else {
                    $objEvent = new CalendarEventsModel();

                    $objEvent->handballnet_id = $arrSpiel['id'];
                    $objEvent->pid = $objCalendar->id;
                    $objEvent->tstamp = time();
                    $objEvent->title = $arrSpiel['tournament']['acronym'].': '.$arrSpiel['homeTeam']['name'].' - '.$arrSpiel['awayTeam']['name'];
                    $objEvent->alias = StringUtil::generateAlias($arrSpiel['homeTeam']['name'].'_'.$arrSpiel['awayTeam']['name'].'_'.$arrSpiel['gameNumber']);
                    $objEvent->h4a_season = $seasonID;
                    $objEvent->gGameID = $handballnetIdParts[2];
                    $objEvent->gGameNo = $arrSpiel['gameNumber'] ?? '';
                    $objEvent->liga_name = $arrSpiel['phase']['name'] ?? '';
                    $objEvent->gClassName = $arrSpiel['phase']['acronym'] ?? '';
                    $objEvent->provider = $handballnetIdParts[0];
                    $objEvent->verband = $handballnetIdParts[1];
                    $objEvent->gHomeTeam = $arrSpiel['homeTeam']['name'];
                    $objEvent->gGuestTeam = $arrSpiel['awayTeam']['name'];

                    $objEvent->author = $objCalendar->h4aEvents_author;
                    $objEvent->source = 'default';
                    $objEvent->addTime = true;
                    $objEvent->startTime = $arrSpiel['startsAt'] / 1000;
                    $objEvent->endTime = $arrSpiel['startsAt'] / 1000 + 5400;
                    $objEvent->startDate = $arrSpiel['startsAt'] / 1000;
                    $objEvent->gGymnasiumNo = $arrSpiel['field']['fieldNumber'];
                    $objEvent->gGymnasiumName = $arrSpiel['field']['name'];
                    $objEvent->location = $arrSpiel['field']['name'];
                    $objEvent->address = $arrSpiel['field']['city'] ?? ''; // TODO adress from api
                    // $objEvent->gGymnasiumStreet = $arrSpiel['gGymnasiumStreet'];
                    $objEvent->gGymnasiumTown = $arrSpiel['field']['city'];
                    // $objEvent->gGymnasiumPostal = $arrSpiel['gGymnasiumPostal'];

                    $objEvent->gComment = $arrSpiel['remark'] ?? '';
                    parse_str(parse_url($arrSpiel['pdfUrl'] ?? '', PHP_URL_QUERY) ?? '', $params);
                    $objEvent->sGID = $params['sGID'] ?? '';
                    $objEvent->published = true;

                    if ('Post' === $arrSpiel['data']['state']) {
                        $objEvent->h4a_resultComplete = true;
                        $objEvent->gHomeGoals = $arrSpiel['homeGoals'] ?? '';
                        $objEvent->gGuestGoals = $arrSpiel['awayGoals'] ?? '';
                        $objEvent->gHomeGoals_1 = $arrSpiel['homeGoalsHalf'] ?? '';
                        $objEvent->gGuestGoals_1 = $arrSpiel['awayGoalsHalf'] ?? '';
                    } else {
                        $objEvent->h4a_resultComplete = false;
                        $objEvent->gHomeGoals = '';
                        $objEvent->gGuestGoals = '';
                        $objEvent->gHomeGoals_1 = '';
                        $objEvent->gGuestGoals_1 = '';
                    }

                    // save new Event
                    $objEvent->save();

                    // Invalidate CacheTag for Event
                    $this->entityCacheTags->invalidateTagsFor($objEvent);
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

            $id = $objEvent->provider.'.'.$objEvent->verband.'.'.$objEvent->gGameID;

            try {
                $data = json_decode($this->handballnetApiClient->getGameSummaryData($id), true);
            } catch (\Exception $e) {
                $this->logger?->error($e->getMessage());
                continue;
            }

            if ('Post' === $data['data']['state']) {
                $objEvent->gHomeGoals = $data['data']['homeGoals'];
                $objEvent->gGuestGoals = $data['data']['awayGoals'];
                $objEvent->gHomeGoals_1 = $data['data']['homeGoalsHalf'];
                $objEvent->gGuestGoals_1 = $data['data']['awayGoalsHalf'];
                $objEvent->h4a_resultComplete = true;
                $objEvent->save();

                $this->logger?->info('Ergebnis ('.$data['data']['homeGoals'].':'.$data['data']['awayGoals'].') für Spiel '.$objEvent->gGameID.' über Handball4all aktualisiert');
            } else {
                $objEvent->h4a_resultComplete = false;

                $this->logger?->info('Ergebnis für Spiel '.$objEvent->gGameID.' über Handball4all geprüft, kein Ergebnis vorhanden');
            }
        }
        $this->redirect($this->getReferer());
    }
}
