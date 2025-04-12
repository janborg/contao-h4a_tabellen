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
use Contao\Message;
use Contao\BackendUser;
use Contao\CalendarModel;
use Contao\CalendarEventsModel;
use Contao\CoreBundle\Cache\EntityCacheTags;
use Janborg\H4aTabellen\Helper\H4aApiHelper;
use Janborg\H4aTabellen\Crawler\TeamsCrawler;
use Janborg\H4aTabellen\Model\H4aSeasonModel;
use Janborg\H4aTabellen\Model\HandballnetTeamsModel;
use Janborg\H4aTabellen\Model\HandballnetSeasonsModel;

class UpdateHandballnetTeamsController extends Backend
{
    public function __construct(
        private TeamsCrawler $teamsCrawler,
        private int $new_teams = 0,
        private int $existing_teams = 0,
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
                        [$team['teamID'], $team['classID'], $team['classShortName']]
                    );
        
                    if ($handballnetTeam) {
                        $this->existing_teams++;
                        continue;
                    }
        
                    $handballnetTeamsModel = new HandballnetTeamsModel();
        
                    $handballnetTeamsModel->saison = $season->hn_season ?? null;
                    $handballnetTeamsModel->team_id = $team['teamID'] ?? null;
                    $handballnetTeamsModel->liga_id = $team['classID'] ?? null;
                    $handballnetTeamsModel->provider = $team['provider'] ?? null;
                    $handballnetTeamsModel->verband = $team['verband'] ?? null;
                    $handballnetTeamsModel->liga_shortname = $team['classShortName'] ?? null;
                    $handballnetTeamsModel->liga_name = isset($team['ligaName']) ? trim(str_replace($team['teamName'], '', $team['ligaName'])) : null;
                    $handballnetTeamsModel->my_team_name = $team['teamName'] ?? null;
                    $handballnetTeamsModel->is_active = true;
                    $handballnetTeamsModel->tstamp = time();
        
                    $handballnetTeamsModel->save();

                    $this->new_teams++;
                }        
            }
        }

        if ($this->new_teams > 0) {
            Message::addConfirmation($this->new_teams.' neue(s) Team(s) erstellt.');
        }
        if ($this->existing_teams > 0) {
            Message::addInfo($this->existing_teams.' existierende(s) Team(s) übersprungen.');
        }

        $this->redirect($this->getReferer());
    }
}
