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

use Janborg\H4aTabellen\HandballNet\DataTransferObject\TeamDto;

class HandballnetTeamsParser
{
    /**
     * @return array<TeamDto>
     */
    public function parseClubTeams(string|null $json): array
    {
        if (null === $json) {
            return [];
        }

        $data = json_decode($json, true);

        if (!\is_array($data) || !\is_array($data['data'] ?? null)) {
            return [];
        }

        return array_filter(
            array_map(
                static function (array $team): TeamDto|null {
                    try {
                        return TeamDto::fromArray($team);
                    } catch (\InvalidArgumentException) {
                        return null;
                    }
                },
                $data['data'],
            ),
        );
    }
}
