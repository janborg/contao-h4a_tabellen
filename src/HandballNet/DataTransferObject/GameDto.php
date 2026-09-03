<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

namespace Janborg\H4aTabellen\HandballNet\DataTransferObject;

use Janborg\H4aTabellen\HandballNet\Enum\GameState;

final class GameDto
{
    public function __construct(
        public readonly string $id,
        public readonly int|null $startsAt,
        public readonly string $tournamentId,
        public readonly string $tournamentName,
        public readonly string $tournamentAcronym,
        public readonly string $phaseId,
        public readonly string $phaseName,
        public readonly string $roundId,
        public readonly string $roundName,
        public readonly string $fieldId,
        public readonly string $fieldName,
        public readonly string $fieldCity,
        public readonly string $homeTeamId,
        public readonly string $homeTeamName,
        public readonly string $awayTeamId,
        public readonly string $awayTeamName,
        public readonly GameState|null $state,
        public readonly int|null $homeGoals,
        public readonly int|null $awayGoals,
        public readonly int|null $homeGoalsHalf,
        public readonly int|null $awayGoalsHalf,
        public readonly string|null $sGID,
    ) {
    }
}
