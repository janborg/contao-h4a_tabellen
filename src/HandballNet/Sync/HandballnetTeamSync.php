<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

namespace Janborg\H4aTabellen\HandballNet\Sync;

use Janborg\H4aTabellen\HandballNet\Parser\HandballnetTeamsParser;
use Janborg\H4aTabellen\HandballnetApiClient;
use Janborg\H4aTabellen\Model\HandballnetSeasonsModel;
use Janborg\H4aTabellen\Model\HandballnetTeamsModel;

class HandballnetTeamSync
{
    public function __construct(
        private HandballnetApiClient $handballnetApiClient,
        private HandballnetTeamsParser $teamsParser,
    ) {
    }

    public function syncTeamsForSeason(HandballnetSeasonsModel $season): void
    {
        $club = $season->getRelated('handballnet_club_id');

        if (null === $club) {
            return;
        }

        $json = $this->handballnetApiClient->getClubTeamsData($club->handballnet_id, $season->season_id);
        $teams = $this->teamsParser->parseClubTeams($json);

        foreach ($teams as $dto) {
            if (null === $dto->ageGroup) {
                continue;
            }

            $model = HandballnetTeamsModel::findOneBy('handballnet_team_id', $dto->id);

            if (null === $model) {
                $model = new HandballnetTeamsModel();
                $model->is_active = true;
            }

            $model->pid = $season->id;
            $model->saison = $season->season_id;
            $model->provider = $dto->provider->value;
            $model->verband = $dto->verband->value;
            $model->age_group = $dto->ageGroup->value ?? '';
            $model->handballnet_team_id = $dto->id;
            $model->my_team_name = $dto->name;
            $model->team_group_id = $dto->teamGroupId;
            $model->liga_name = $dto->tournamentName;
            $model->liga_shortname = $dto->tournamentAcronym;
            $model->handballnet_tournament_id = $dto->tournamentId;
            $model->tournament_type = $dto->tournamentType;
            $model->tstamp = time();
            $model->save();
        }
    }
}
