<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

namespace Janborg\H4aTabellen\HandballNet\Parser;

use Janborg\H4aTabellen\HandballNet\DataTransferObject\GameDto;
use Janborg\H4aTabellen\HandballNet\Enum\GameState;

class HandballnetGameScheduleParser
{
    /**
     * @param array<mixed> $data
     *
     * @return array<GameDto>
     */
    public function parseSchedule(array $data): array
    {
        $games = [];

        foreach ($data['data'] ?? [] as $arrSpiel) {
            $sGID = null;

            if (!empty($arrSpiel['pdfUrl'])) {
                parse_str(parse_url($arrSpiel['pdfUrl'], PHP_URL_QUERY) ?: '', $params);
                $sGID = $params['sGID'] ?? null;
            }

            $games[] = new GameDto(
                id: $arrSpiel['id'],
                startsAt: isset($arrSpiel['startsAt']) ? (int) ($arrSpiel['startsAt'] / 1000) : null,
                tournamentId: $arrSpiel['tournament']['id'] ?? '',
                tournamentName: $arrSpiel['tournament']['name'] ?? '',
                tournamentAcronym: $arrSpiel['tournament']['acronym'] ?? '',
                phaseId: $arrSpiel['phase']['id'] ?? '',
                phaseName: $arrSpiel['phase']['name'] ?? '',
                roundId: $arrSpiel['round']['id'] ?? '',
                roundName: $arrSpiel['round']['name'] ?? '',
                fieldId: $arrSpiel['field']['id'] ?? '',
                fieldName: $arrSpiel['field']['name'] ?? '',
                fieldCity: $arrSpiel['field']['city'] ?? '',
                homeTeamId: $arrSpiel['homeTeam']['id'] ?? '',
                homeTeamName: $arrSpiel['homeTeam']['name'] ?? '',
                awayTeamId: $arrSpiel['awayTeam']['id'] ?? '',
                awayTeamName: $arrSpiel['awayTeam']['name'] ?? '',
                state: GameState::tryFrom($arrSpiel['state'] ?? ''),
                homeGoals: $arrSpiel['homeGoals'] ?? null,
                awayGoals: $arrSpiel['awayGoals'] ?? null,
                homeGoalsHalf: $arrSpiel['homeGoalsHalf'] ?? null,
                awayGoalsHalf: $arrSpiel['awayGoalsHalf'] ?? null,
                sGID: $sGID,
            );
        }

        return $games;
    }
}
