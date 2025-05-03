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
use Janborg\H4aTabellen\Crawler\TeamsCrawler;
use Janborg\H4aTabellen\Model\H4aSeasonModel;
use Janborg\H4aTabellen\Model\HandballnetTeamsModel;

class UpdateHandballnetTeamsController extends Backend
{
    public function __construct(
        private TeamsCrawler $teamsCrawler,
        private int $new_teams = 0,
        private int $existing_teams = 0,
        private int $active_seasons = 0,
        private int $teams_without_id = 0,
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
            $this->teamsCrawler->setClubID($season->club_id);
            $this->teamsCrawler->setProvider($season->provider);
            $this->teamsCrawler->setVerbandName($season->verband);
            $this->teamsCrawler->setSeason($season->hn_season);

            $teams = $this->teamsCrawler->getAllTeams();

            if ($teams) {
                foreach ($teams as $team) {
                    $handballnetTeam = HandballnetTeamsModel::findBy(
                        ['team_id=?', 'liga_id=?', 'liga_shortname=?'],
                        [$team->team_id, $team->liga_id, $team->liga_short_name],
                    );

                    // skip teams that already exist
                    if ($handballnetTeam) {
                        ++$this->existing_teams;
                        continue;
                    }
                    
                    // skip teams without team_id or liga_id
                    if (empty($team->team_id) || empty($team->liga_id)) {
                        ++$this->teams_without_id;
                        continue;
                    }       

                    // create new teams
                    $handballnetTeamsModel = new HandballnetTeamsModel();

                    $handballnetTeamsModel->saison = $season->hn_season;
                    $handballnetTeamsModel->team_id = $team->team_id;
                    $handballnetTeamsModel->liga_id = $team->liga_id;
                    $handballnetTeamsModel->provider = $team->provider->toString();
                    $handballnetTeamsModel->verband = $team->verband->toString();
                    $handballnetTeamsModel->liga_shortname = $team->liga_short_name;
                    $handballnetTeamsModel->liga_name = $team->liga_name;
                    $handballnetTeamsModel->my_team_name = $team->team_name;
                    $handballnetTeamsModel->is_active = true;
                    $handballnetTeamsModel->tstamp = time();

                    $handballnetTeamsModel->save();

                    ++$this->new_teams;
                }
            }
        }

        if ($this->active_seasons > 0) {
            Message::addConfirmation($this->active_seasons.' aktive Saison(s) gefunden und aktualisiert.');
        }

        if ($this->new_teams > 0) {
            Message::addConfirmation($this->new_teams.' neue(s) Team(s) erstellt.');
        }
        if ($this->existing_teams > 0) {
            Message::addInfo($this->existing_teams.' existierende(s) Team(s) gefunden.');
        }
        if ($this->teams_without_id > 0) {
            Message::addError($this->teams_without_id.' Team(s) ohne ID nicht angelegt.');
        }

        $this->redirect($this->getReferer());
    }
}
