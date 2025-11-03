<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

namespace Janborg\H4aTabellen\Backend;

use Contao\Backend;
use Contao\BackendUser;
use Contao\Message;
use Janborg\H4aTabellen\HandballnetApiClient;
use Janborg\H4aTabellen\Model\H4aSeasonModel;
use Janborg\H4aTabellen\Model\HandballnetTeamsModel;

class UpdateHandballnetTeamsController extends Backend
{
    public function __construct(
        private HandballnetApiClient $handballnetApiClient,
        private int $new_teams = 0,
        private int $existing_teams = 0,
        private int $active_seasons = 0,
    ) {
        parent::__construct();
        $this->import(BackendUser::class, 'User');
    }

    public function updateTeams(): void
    {
        $objSeasons = H4aSeasonModel::findBy(
            ['is_active = ?'],
            [true],
        );

        if (null === $objSeasons) {
            Message::addError('Es sind keine aktiven Saisons vorhanden.');
            $this->redirect($this->getReferer());
        }

        $this->active_seasons = $objSeasons->count();

        foreach ($objSeasons as $season) {
            $id = $season->provider.'.'.$season->verband.'.'.$season->club_id;

            try {
                $data = json_decode($this->handballnetApiClient->getClubTeamsData($id, $season->hn_season), true);
            } catch (\Exception $e) {
                Message::addError($e->getMessage());
                continue;
            }

            foreach ($data['data'] as $team) {
                $handballnetTeam = HandballnetTeamsModel::findBy(
                    ['handballnet_team_id=?'],
                    [$team['id']],
                );

                // skip teams that already exist
                if ($handballnetTeam) {
                    // update existing team
                    $handballnetTeamsModel = $handballnetTeam;
                    ++$this->existing_teams;
                } else {
                    // create new team
                    $handballnetTeamsModel = new HandballnetTeamsModel();
                    ++$this->new_teams;
                }

                $teamIdParts = explode('.', $team['id']);

                $handballnetTeamsModel->pid = $season->id;
                $handballnetTeamsModel->saison = $season->hn_season;

                $handballnetTeamsModel->provider = $teamIdParts[0];
                $handballnetTeamsModel->verband = $teamIdParts[1];

                $handballnetTeamsModel->liga_shortname = $team['defaultTournament']['acronym'];
                $handballnetTeamsModel->liga_name = $team['defaultTournament']['name'];
                $handballnetTeamsModel->handballnet_tournament_id = $team['defaultTournament']['id'];

                $handballnetTeamsModel->team_id = $teamIdParts[2];
                $handballnetTeamsModel->handballnet_team_id = $team['id'];
                $handballnetTeamsModel->my_team_name = $team['name'];
                $handballnetTeamsModel->is_active = true;
                $handballnetTeamsModel->tstamp = time();

                $handballnetTeamsModel->save();
            }
        }

        if ($this->active_seasons > 0) {
            Message::addConfirmation($this->active_seasons.' aktive Saison(s) gefunden und aktualisiert.');
        }

        if ($this->new_teams > 0) {
            Message::addConfirmation($this->new_teams.' neue(s) Team(s) erstellt.');
        }
        if ($this->existing_teams > 0) {
            Message::addInfo($this->existing_teams.' existierende(s) Team(s) aktualisiert.');
        }

        $this->redirect($this->getReferer());
    }
}
