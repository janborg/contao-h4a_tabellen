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

use Janborg\H4aTabellen\HandballnetApiClient;
use Janborg\H4aTabellen\Model\HandballnetClubsModel;
use Janborg\H4aTabellen\Model\HandballnetSeasonsModel;

class HandballnetSeasonSync
{
    public function __construct(private HandballnetApiClient $handballnetApiClient)
    {
    }

    /**
     * @return array<HandballnetSeasonsModel>
     */
    public function syncSeasonsForClub(HandballnetClubsModel $club, string $year): array
    {
        $data = json_decode(
            $this->handballnetApiClient->getClubTeamsData($club->handballnet_id, $year),
            true,
        );

        $clubSeasons = $data['meta']['facets']['0']['values'] ?? [];
        $seasons = [];

        foreach ($clubSeasons as $season) {
            $model = HandballnetSeasonsModel::findOneBy(
                ['handballnet_club_id=?', 'season_id=?'],
                [$club->id, $season['id']],
            );

            if (null === $model) {
                $model = new HandballnetSeasonsModel();
                $model->is_active = true;
                $model->tstamp = time();
                $model->pid = $club->id;
            }

            $model->season_id = $season['id'];
            $model->season_name = $season['name'];
            $model->handballnet_club_id = $club->id;
            $model->club_name = $club->name;
            $model->save();

            $seasons[] = $model;
        }

        return $seasons;
    }
}
