<?php

declare(strict_types=1);

namespace Janborg\H4aTabellen\HandballNet;

use Contao\CalendarEventsModel;
use Contao\CalendarModel;
use Contao\CoreBundle\Cache\EntityCacheTags;
use Janborg\H4aTabellen\HandballNet\DataTransferObject\GameDto;
use Janborg\H4aTabellen\HandballNet\Enum\GameState;
use Janborg\H4aTabellen\HandballNet\Mapper\CalendarEventMapper;
use Janborg\H4aTabellen\HandballNet\Parser\HandballnetGameScheduleParser;
use Janborg\H4aTabellen\HandballnetApiClient;
use Janborg\H4aTabellen\Model\HandballnetTeamsModel;
use Psr\Log\LoggerInterface;

class HandballnetEventAutomator
{
    public function __construct(
        private EntityCacheTags $entityCacheTags,
        private HandballnetApiClient $handballnetApiClient,
        private HandballnetGameScheduleParser $scheduleParser,
        private CalendarEventMapper $eventMapper,
        private readonly LoggerInterface|null $logger,
    ) {
    }

    public function syncCalendars(CalendarModel $objCalendar): void
    {
        $team = HandballnetTeamsModel::findOneByHandballnet_team_id($objCalendar->handballnet_team_id);

        if (null === $team) {
            $this->logger?->warning('Kein Team mit handballnet_team_id "'.$objCalendar->handballnet_team_id.'" gefunden.');

            return;
        }

        $teamsToSync = $objCalendar->add_team_group_id_events
            ? HandballnetTeamsModel::findByTeam_group_id($team->team_group_id)
            : HandballnetTeamsModel::findByHandballnet_team_id($objCalendar->handballnet_team_id);

        if (null === $teamsToSync) {
            return;
        }

        foreach ($teamsToSync as $syncTeam) {
            $this->syncTeamSchedule($objCalendar, $syncTeam);
        }
    }

    public function updateResults(): void
    {
        $objEvents = CalendarEventsModel::findBy(
            ['DATE(FROM_UNIXTIME(startDate)) = ?', 'hn_resultComplete != ?'],
            [date('Y-m-d'), true],
        );

        if (null === $objEvents) {
            return;
        }

        foreach ($objEvents as $objEvent) {
            if ($objEvent->startTime > time() || '00:00' === date('H:i', (int) $objEvent->startTime)) {
                continue;
            }

            try {
                $data = json_decode(
                    $this->handballnetApiClient->getGameSummaryData($objEvent->handballnet_game_id),
                    true,
                );
            } catch (\Exception $e) {
                $this->logger?->error($e->getMessage());
                continue;
            }

            $this->applyGameResult($objEvent, $data['data'] ?? []);
        }
    }

    private function syncTeamSchedule(CalendarModel $objCalendar, HandballnetTeamsModel $team): void
    {
        try {
            $data = json_decode(
                $this->handballnetApiClient->getTeamScheduleData($team->handballnet_team_id),
                true,
            );
        } catch (\Exception $e) {
            $this->logger?->error($e->getMessage());

            return;
        }

        if (isset($data['code']) && '400' === $data['code']) {
            $this->logger?->info('Update für Kalender "'.$objCalendar->title.'" (ID: '.$objCalendar->id.') abgebrochen, prüfen Sie die handballnet ID!');

            return;
        }

        if (!isset($data['data']) || !\is_array($data['data'])) {
            return;
        }

        foreach ($this->scheduleParser->parseSchedule($data) as $gameDto) {
            $this->syncGame($objCalendar, $team, $gameDto);
        }
    }

    private function syncGame(CalendarModel $objCalendar, HandballnetTeamsModel $team, GameDto $gameDto): void
    {
        $isNew = false;
        $event = CalendarEventsModel::findOneBy(
            ['pid=?', 'handballnet_game_id=?'],
            [$objCalendar->id, $gameDto->id],
        );

        if (null === $event) {
            $event = new CalendarEventsModel();
            $event->pid = $objCalendar->id;
            $isNew = true;
        }

        $changed = $this->eventMapper->apply($event, $gameDto, $team->pid, $objCalendar->h4aEvents_author, $isNew);

        if (!$changed) {
            return;
        }

        $event->save();
        $this->logger?->info('Event für Spiel '.$gameDto->tournamentAcronym.': '.$gameDto->homeTeamName.' - '.$gameDto->awayTeamName.' (gID: '.$gameDto->id.') aktualisiert');
        $this->entityCacheTags->invalidateTagsFor($event);
    }

    /**
     * Undocumented function.
     *
     * @param array<mixed> $data
     */
    private function applyGameResult(CalendarEventsModel $objEvent, array $data): void
    {
        $state = GameState::tryFrom($data['state'] ?? '');

        if (null !== $state) {
            $objEvent->handballnet_state = $state->value;
        }

        if (GameState::POST === $state) {
            $objEvent->homeGoals = $data['homeGoals'] ?? '';
            $objEvent->awayGoals = $data['awayGoals'] ?? '';
            $objEvent->homeGoalsHalf = $data['homeGoalsHalf'] ?? '';
            $objEvent->awayGoalsHalf = $data['awayGoalsHalf'] ?? '';
            $objEvent->hn_resultComplete = true;
            $objEvent->save();

            $this->logger?->info('Ergebnis ('.$objEvent->homeGoals.':'.$objEvent->awayGoals.') für Spiel '.$objEvent->handballnet_game_id.' aktualisiert');
        } else {
            $objEvent->hn_resultComplete = false;
            $this->logger?->info('Ergebnis für Spiel '.$objEvent->handballnet_game_id.' geprüft, kein Ergebnis vorhanden');
        }
    }
}
