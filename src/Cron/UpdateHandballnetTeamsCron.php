<?php

declare(strict_types=1);

namespace Janborg\H4aTabellen\Cron;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCronJob;
use Contao\CoreBundle\Framework\ContaoFramework;
use Janborg\H4aTabellen\Event\HandballnetTeamCreatedEvent;
use Janborg\H4aTabellen\HandballNet\AgeGroup;
use Janborg\H4aTabellen\HandballNet\Provider;
use Janborg\H4aTabellen\HandballNet\Verband;
use Janborg\H4aTabellen\HandballnetApiClient;
use Janborg\H4aTabellen\Model\H4aSeasonModel;
use Janborg\H4aTabellen\Model\HandballnetTeamsModel;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsCronJob('daily', method: 'updateHandballnetTeams')]
class UpdateHandballnetTeamsCron
{
    public function __construct(
        private ContaoFramework $contaoFramework,
        private HandballnetApiClient $handballnetApiClient,
        private EventDispatcherInterface $eventDispatcher,
        private readonly LoggerInterface|null $contaoCronLogger,
        private int $active_seasons = 0,
        private int $total_teams_checked = 0,
        private int $existing_teams = 0,
        private int $new_teams = 0,
    ) {
        $this->contaoFramework->initialize();
    }

    public function updateHandballnetTeams(): void
    {
        // Find active Seasons
        $objSeasons = H4aSeasonModel::findBy(
            ['is_active=?'],
            [true],
            ['order' => 'hn_season ASC'],
        );

        if (null === $objSeasons) {
            return;
        }

        foreach ($objSeasons as $season) {
            ++$this->active_seasons;

            $id = $season->provider.'.'.$season->verband.'.'.$season->club_id;

            try {
                $data = json_decode($this->handballnetApiClient->getClubTeamsData($id, $season->hn_season), true);
            } catch (\Exception $e) {
                $this->contaoCronLogger->error('Fehler beim Abruf über die handballnetApi', [$e->getMessage()]);
            }

            if (empty($data['data']) || !\is_array($data['data'])) {
                continue;
            }

            foreach ($data['data'] as $team) {
                ++$this->total_teams_checked;

                $handballnetTeam = HandballnetTeamsModel::findBy(
                    ['handballnet_id=?'],
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
                $agegroup = AgeGroup::tryFrom($team['defaultTournament']['ageGroup']);

                if (null === $provider) {
                    $this->contaoCronLogger->info(
                        'Unbekannter Provider '.$teamIdParts[0],
                    );
                    continue;
                }

                if (null === $verband) {
                    $this->contaoCronLogger->info(
                        'Unbekannter Verband '.$teamIdParts[1],
                    );
                    continue;
                }

                if (null === $agegroup) {
                    $this->contaoCronLogger->info(
                        'Unbekannte Altergruppe '.$team['defaultTournament']['ageGroup'].' bei Team '.$team['id'].' ('.$team['name'].') ',
                    );
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

                $handballnetTeam->save();

                ++$this->new_teams;

                // Dispatch Event for every created Event
                $this->eventDispatcher->dispatch(
                    new HandballnetTeamCreatedEvent($handballnetTeam, $season),
                );

                // Log created Event
                $this->contaoCronLogger->info(
                    'Neues Team in der Saison '.$season->hn_season.' erstellt ('.$team['name'].', '.$team['defaultTournament']['acronym'].').',
                );
            }
        }

        // Log sum_up
        $this->contaoCronLogger->info(
            'HandballnetTeams Update: '.$this->active_seasons.' aktive Saisons, '.$this->total_teams_checked.' Teams geprüft, '.$this->existing_teams.' existierende Teams, '.$this->new_teams.' neue Teams.',
        );
    }
}
