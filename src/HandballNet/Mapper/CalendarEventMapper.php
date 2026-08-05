<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

namespace Janborg\H4aTabellen\HandballNet\Mapper;

use Contao\CalendarEventsModel;
use Contao\StringUtil;
use Janborg\H4aTabellen\HandballNet\DataTransferObject\GameDto;
use Janborg\H4aTabellen\HandballNet\Enum\GameState;

class CalendarEventMapper
{
    /**
     * Wendet die Daten aus dem DTO auf das Model an. Gibt zurück, ob sich etwas
     * geändert hat (nur relevant für Update).
     */
    public function apply(CalendarEventsModel $event, GameDto $dto, int $seasonPid, int $authorId, bool $isNew): bool
    {
        $changed = $isNew;

        $changed = $this->applyIfChanged($event, 'handballnet_tournament_id', $dto->tournamentId, $changed);
        $changed = $this->applyIfChanged($event, 'handballnet_tournament_name', $dto->tournamentName, $changed);
        $changed = $this->applyIfChanged($event, 'handballnet_phase_id', $dto->phaseId, $changed);
        $changed = $this->applyIfChanged($event, 'handballnet_phase_name', $dto->phaseName, $changed);
        $changed = $this->applyIfChanged($event, 'handballnet_round_id', $dto->roundId, $changed);
        $changed = $this->applyIfChanged($event, 'handballnet_round_name', $dto->roundName, $changed);
        $changed = $this->applyIfChanged($event, 'handballnet_field_id', $dto->fieldId, $changed);
        $changed = $this->applyIfChanged($event, 'handballnet_field_name', $dto->fieldName, $changed);
        $changed = $this->applyIfChanged($event, 'homeTeam_id', $dto->homeTeamId, $changed);
        $changed = $this->applyIfChanged($event, 'homeTeam_name', $dto->homeTeamName, $changed);
        $changed = $this->applyIfChanged($event, 'awayTeam_id', $dto->awayTeamId, $changed);
        $changed = $this->applyIfChanged($event, 'awayTeam_name', $dto->awayTeamName, $changed);

        if (null !== $dto->sGID) {
            $changed = $this->applyIfChanged($event, 'sGID', $dto->sGID, $changed);
        }

        if (null !== $dto->startsAt && $dto->startsAt !== $event->startTime) {
            $event->startTime = $dto->startsAt;
            $event->endTime = $dto->startsAt + 5400;
            $event->startDate = $dto->startsAt;
            $changed = true;
        }

        if (null !== $dto->state && $dto->state->value !== $event->handballnet_state) {
            $event->handballnet_state = $dto->state->value;
            $changed = true;
        }

        $event->handballnet_game_id = $dto->id;
        $event->handballnet_season = $seasonPid;
        $event->author = $authorId;
        $event->source = 'default';
        $event->addTime = true;

        if (GameState::POST === $dto->state) {
            $event->hn_resultComplete = true;
            $event->homeGoals = $dto->homeGoals ?? '';
            $event->awayGoals = $dto->awayGoals ?? '';
            $event->homeGoalsHalf = $dto->homeGoalsHalf ?? '';
            $event->awayGoalsHalf = $dto->awayGoalsHalf ?? '';
        } else {
            $event->hn_resultComplete = false;
        }

        if ($isNew) {
            $event->tstamp = time();
            $event->title = $dto->tournamentAcronym.': '.$dto->homeTeamName.' - '.$dto->awayTeamName;
            $event->alias = StringUtil::generateAlias($dto->homeTeamName.'_'.$dto->awayTeamName.'_'.$dto->id);
            $event->location = $dto->fieldName;
            $event->address = $dto->fieldCity;
            $event->published = true;
        }

        return $changed;
    }

    private function applyIfChanged(CalendarEventsModel $event, string $field, string $value, bool $changed): bool
    {
        if ($event->{$field} !== $value) {
            $event->{$field} = $value;

            return true;
        }

        return $changed;
    }
}
