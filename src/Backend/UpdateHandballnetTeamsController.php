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
use Janborg\H4aTabellen\HandballNet\AgeGroup;
use Janborg\H4aTabellen\HandballNet\Provider;
use Janborg\H4aTabellen\HandballNet\Verband;
use Janborg\H4aTabellen\HandballnetApiClient;
use Janborg\H4aTabellen\Model\HandballnetSeasonsModel;
use Janborg\H4aTabellen\Model\HandballnetTeamsModel;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UpdateHandballnetTeamsController extends Backend
{
    public function __construct(
        private HandballnetApiClient $handballnetApiClient,
        private UrlGeneratorInterface $urlGenerator,
        private int $new_teams = 0,
        private int $existing_teams = 0,
        private int $active_seasons = 0,
    ) {
        parent::__construct();
        $this->import(BackendUser::class, 'User');
    }

    public function updateTeams(): void
    {
        $objSeasons = HandballnetSeasonsModel::findBy(
            ['is_active = ?'],
            [true],
        );

        if (null === $objSeasons) {
            Message::addError('Es sind keine aktiven Saisons vorhanden.');
            $this->redirect($this->getReferer());
        }

        $this->active_seasons = $objSeasons->count();

        foreach ($objSeasons as $season) {
            // $id = $season->provider.'.'.$season->verband.'.'.$season->club_id;

            try {
                $data = json_decode($this->handballnetApiClient->getClubTeamsData($season->handballnet_club_id, $season->season_id), true);
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

                    $handballnetTeamsModel->is_active = true;
                    $handballnetTeamsModel->tstamp = time();
                }

                $teamIdParts = explode('.', $team['id']);

                $handballnetTeamsModel->pid = $season->id;
                $handballnetTeamsModel->saison = $season->season_id;

                $provider = Provider::tryFrom($teamIdParts[0]);
                $verband = Verband::tryFrom($teamIdParts[1]);
                $agegroup = AgeGroup::tryFrom($team['defaultTournament']['ageGroup'] ?? '');

                if (null === $provider) {
                    Message::addError('Unbekannter Provider '.$teamIdParts[0]);
                    continue;
                }

                if (null === $verband) {
                    Message::addError('Unbekannter Verband '.$teamIdParts[1]);
                    continue;
                }

                if (null === $agegroup) {
                    Message::addError('Unbekannte Altergruppe '.$team['defaultTournament']['ageGroup']);
                    continue;
                }

                $handballnetTeamsModel->provider = $provider->value;
                $handballnetTeamsModel->verband = $verband->value;
                $handballnetTeamsModel->age_group = $agegroup->value;

                $handballnetTeamsModel->liga_name = $team['defaultTournament']['name'];
                $handballnetTeamsModel->handballnet_tournament_id = $team['defaultTournament']['id'];
                $handballnetTeamsModel->tournament_type = $team['defaultTournament']['tournamentType'];
                $handballnetTeamsModel->liga_shortname = $team['defaultTournament']['acronym'];

                $handballnetTeamsModel->handballnet_team_id = $team['id'];
                $handballnetTeamsModel->my_team_name = $team['name'];
                $handballnetTeamsModel->team_group_id = $team['teamGroupId'];

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

        $this->redirect($this->urlGenerator->generate('contao_backend', ['do' => 'handballnet_teams']));
    }
}
