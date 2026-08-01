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
use Contao\StringUtil;
use Janborg\H4aTabellen\HandballNet\GameState;
use Janborg\H4aTabellen\HandballnetApiClient;
use Janborg\H4aTabellen\Model\HandballnetTeamsModel;
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

    /**
     * Update Calendars via handball.net.
     */
    public function syncCalendars(CalendarModel $objCalendar, bool $cache = true): void
    {
        // check for other seasons
        $team_group_id = HandballnetTeamsModel::findOneByHandballnet_team_id($objCalendar->handballnet_team_id)->team_group_id;

        if ($objCalendar->add_team_group_id_events) {
            $teamsToSync = HandballnetTeamsModel::findByTeam_group_id($team_group_id);
        } else {
            $teamsToSync = HandballnetTeamsModel::findByHandballnet_team_id($objCalendar->handballnet_team_id);
        }

        foreach ($teamsToSync as $team) {
            // $objHandballnetSeason =
            // HandballnetSeasonsModel::findById($arrSeason['h4a_saison'])->pid;

            try {
                $data = json_decode($this->handballnetApiClient->getTeamScheduleData($team->handballnet_team_id), true);
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

            foreach ($arrSpiele as $arrSpiel) {
                $objEvent = CalendarEventsModel::findOneBy(
                    ['pid=?', 'handballnet_game_id=?'],
                    [$objCalendar->id, $arrSpiel['id']],
                );

                // Update, wenn ModelObjekt existiert
                if (null !== $objEvent) {
                    $isChanged = false;

                    $objEvent->handballnet_season = $team->pid;
                    $objEvent->author = $objCalendar->h4aEvents_author;
                    $objEvent->source = 'default';
                    $objEvent->addTime = true;
                    //                   $objEvent->handballnet_id = $arrSpiel['id'] ?? ''; Check, if
                    // tournament id or name changed
                    if (
                        $objEvent->handballnet_tournament_id !== $arrSpiel['tournament']['id']
                        || $objEvent->handballnet_tournament_name !== $arrSpiel['tournament']['name']
                    ) {
                        $objEvent->handballnet_tournament_id = $arrSpiel['tournament']['id'] ?? '';
                        $objEvent->handballnet_tournament_name = $arrSpiel['tournament']['name'] ?? '';
                        $isChanged = true;
                    }

                    // Check, if Phase id or name changed
                    if (
                        $objEvent->handballnet_phase_id !== $arrSpiel['phase']['id']
                        || $objEvent->handballnet_phase_name !== $arrSpiel['phase']['name']
                    ) {
                        $objEvent->handballnet_phase_id = $arrSpiel['phase']['id'] ?? '';
                        $objEvent->handballnet_phase_name = $arrSpiel['phase']['name'] ?? '';
                        $isChanged = true;
                    }
                    // Check, if round id or name changed
                    if (
                        $objEvent->handballnet_round_id !== $arrSpiel['round']['id']
                        || $objEvent->handballnet_round_name !== $arrSpiel['round']['name']
                    ) {
                        $objEvent->handballnet_round_id = $arrSpiel['round']['id'] ?? '';
                        $objEvent->handballnet_round_name = $arrSpiel['round']['name'] ?? '';
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
                        $objEvent->homeTeam_id !== $arrSpiel['homeTeam']['id']
                        || $objEvent->awayTeam_id !== $arrSpiel['awayTeam']['id']
                        || $objEvent->homeTeam_name !== $arrSpiel['homeTeam']['name']
                        || $objEvent->awayTeam_name !== $arrSpiel['awayTeam']['name']
                    ) {
                        $objEvent->homeTeam_id = $arrSpiel['homeTeam']['id'];
                        $objEvent->awayTeam_id = $arrSpiel['awayTeam']['id'];
                        $objEvent->homeTeam_name = $arrSpiel['homeTeam']['name'];
                        $objEvent->awayTeam_name = $arrSpiel['awayTeam']['name'];
                        $isChanged = true;
                    }

                    // Check, if handballnet_field changed
                    if ($objEvent->handballnet_field_id !== $arrSpiel['field']['id']) {
                        $objEvent->handballnet_field_id = $arrSpiel['field']['id'];
                        $isChanged = true;
                    }

                    // check if reportUrl changed
                    if (null !== $arrSpiel['pdfUrl'] && '' !== $arrSpiel['pdfUrl']) {
                        parse_str(parse_url($arrSpiel['pdfUrl'], PHP_URL_QUERY), $params);

                        $sGID = $params['sGID'] ?? null;

                        if (null !== $sGID && $objEvent->sGID !== $sGID) {
                            $objEvent->sGID = $sGID;
                            $isChanged = true;
                        }
                    }

                    // Check, if state changed
                    if ($objEvent->handballnet_state !== $arrSpiel['state']) {
                        try {
                            $objEvent->handballnet_state = GameState::from($arrSpiel['state'])->value;
                        } catch (\ValueError $e) {
                            $this->logger?->warning('Unbekannter handballnet_state "'.$arrSpiel['state'].'" für Spiel '.$arrSpiel['id']);
                        }
                        $isChanged = true;
                    }

                    // TODO: How to check if it has changed ?!
                    if ('Post' === $arrSpiel['state']) {
                        $objEvent->homeGoals = $arrSpiel['homeGoals'];
                        $objEvent->awayGoals = $arrSpiel['awayGoals'];
                        $objEvent->homeGoalsHalf = $arrSpiel['homeGoalsHalf'] ?? '';
                        $objEvent->awayGoalsHalf = $arrSpiel['awayGoalsHalf'] ?? '';
                        $objEvent->hn_resultComplete = true;
                    } else {
                        $objEvent->hn_resultComplete = false;
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

                    // --- Standard Contao Felder ---
                    $objEvent->pid = $objCalendar->id;
                    $objEvent->tstamp = time();
                    $objEvent->title = $arrSpiel['tournament']['acronym'].': '.$arrSpiel['homeTeam']['name'].' - '.$arrSpiel['awayTeam']['name'];
                    $objEvent->alias = StringUtil::generateAlias($arrSpiel['homeTeam']['name'].'_'.$arrSpiel['awayTeam']['name'].'_'.$arrSpiel['id']);
                    $objEvent->author = $objCalendar->h4aEvents_author;
                    $objEvent->source = 'default';
                    $objEvent->addTime = true;
                    $objEvent->startTime = $arrSpiel['startsAt'] / 1000;
                    $objEvent->endTime = $arrSpiel['startsAt'] / 1000 + 5400;
                    $objEvent->startDate = $arrSpiel['startsAt'] / 1000;
                    $objEvent->location = $arrSpiel['field']['name'];
                    $objEvent->address = $arrSpiel['field']['city'] ?? '';

                    // sGID aus PDF-URL extrahieren
                    parse_str(parse_url($arrSpiel['pdfUrl'] ?? '', PHP_URL_QUERY) ?: '', $params);
                    $objEvent->sGID = $params['sGID'] ?? '';
                    $objEvent->published = true;

                    // --- H4A / Handballnet spezifische Felder (mapped to new DCA) ---
                    $objEvent->handballnet_game_id = $arrSpiel['id'];
                    $objEvent->handballnet_season = $team->pid;

                    // ID-Teile auf neue DCA-Felder mappen (Index ggf. an deine API-Struktur anpassen)
                    $objEvent->handballnet_tournament_id = $arrSpiel['tournament']['id'] ?? '';
                    $objEvent->handballnet_tournament_name = $arrSpiel['tournament']['name'] ?? '';
                    $objEvent->handballnet_phase_id = $arrSpiel['phase']['id'] ?? '';
                    $objEvent->handballnet_phase_name = $arrSpiel['phase']['name'] ?? '';
                    $objEvent->handballnet_round_id = $arrSpiel['round']['id'] ?? '';
                    $objEvent->handballnet_round_name = $arrSpiel['round']['name'] ?? '';
                    $objEvent->handballnet_field_id = $arrSpiel['field']['id'] ?? '';
                    $objEvent->handballnet_field_name = $arrSpiel['field']['name'] ?? '';

                    // Team Namen & IDs (umbenannt von gHomeTeam/gGuestTeam)
                    $objEvent->homeTeam_name = $arrSpiel['homeTeam']['name'] ?? '';
                    $objEvent->homeTeam_id = $arrSpiel['homeTeam']['id'] ?? '';
                    $objEvent->awayTeam_name = $arrSpiel['awayTeam']['name'] ?? '';
                    $objEvent->awayTeam_id = $arrSpiel['awayTeam']['id'] ?? '';

                    // Liga & Klassifizierung
                    $objEvent->liga_name = $arrSpiel['phase']['name'] ?? '';
                    $objEvent->gClassName = $arrSpiel['phase']['acronym'] ?? '';

                    // Spielstatus (Enum)
                    try {
                        $objEvent->handballnet_state = GameState::from($arrSpiel['state'] ?? '')->value;
                    } catch (\ValueError $e) {
                        $objEvent->handballnet_state = '';
                        $this->logger?->warning('Unbekannter handballnet_state "'.($arrSpiel['state'] ?? '').'" für Spiel '.$arrSpiel['id']);
                    }

                    // Spielstatus & Ergebnisse (mapped to new DCA field names)
                    if (GameState::POST->value === ($arrSpiel['state'] ?? '')) {
                        $objEvent->hn_resultComplete = true;
                        $objEvent->homeGoals = $arrSpiel['homeGoals'] ?? '';
                        $objEvent->awayGoals = $arrSpiel['awayGoals'] ?? '';
                        $objEvent->homeGoalsHalf = $arrSpiel['homeGoalsHalf'] ?? '';
                        $objEvent->awayGoalsHalf = $arrSpiel['awayGoalsHalf'] ?? '';
                    } else {
                        $objEvent->hn_resultComplete = false;
                        $objEvent->homeGoals = '';
                        $objEvent->awayGoals = '';
                        $objEvent->homeGoalsHalf = '';
                        $objEvent->awayGoalsHalf = '';
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
            ['DATE(FROM_UNIXTIME(startDate)) = ?', 'hn_resultComplete != ?'],
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

            try {
                $data = json_decode($this->handballnetApiClient->getGameSummaryData($objEvent->handballnet_game_id), true);
            } catch (\Exception $e) {
                $this->logger?->error($e->getMessage());
                continue;
            }

            try {
                $objEvent->handballnet_state = GameState::from($data['data']['state'])->value;
            } catch (\ValueError $e) {
                $this->logger?->warning('Unbekannter handballnet_state "'.$data['data']['state'].'" für Spiel '.$objEvent->handballnet_game_id);
            }

            if (GameState::POST->value === $objEvent->handballnet_state) {
                $objEvent->homeGoals = $data['data']['homeGoals'];
                $objEvent->awayGoals = $data['data']['awayGoals'];
                $objEvent->homeGoalsHalf = $data['data']['homeGoalsHalf'];
                $objEvent->awayGoalsHalf = $data['data']['awayGoalsHalf'];
                $objEvent->hn_resultComplete = true;
                $objEvent->save();

                $this->logger?->info('Ergebnis ('.$data['data']['homeGoals'].':'.$data['data']['awayGoals'].') für Spiel '.$objEvent->handballnet_game_id.' über handball.net aktualisiert');
            } else {
                $objEvent->hn_resultComplete = false;

                $this->logger?->info('Ergebnis für Spiel '.$objEvent->handballnet_game_id.' über handball.net geprüft, kein Ergebnis vorhanden');
            }
        }
        $this->redirect($this->getReferer());
    }
}
