<?php

declare(strict_types=1);

namespace Janborg\H4aTabellen\Cron;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCronJob;
use Contao\CoreBundle\Framework\ContaoFramework;
use Janborg\H4aTabellen\Crawler\TeamsCrawler;
use Janborg\H4aTabellen\Event\HandballnetTeamCreatedEvent;
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
        private int $new_teams_created = 0,
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
                $data = json_decode($this->handballnetApiClient->getClubTeamsData($id, $season->hn_season, false), true);
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

                // Continue, if team already exists
                if ($handballnetTeam) {
                    ++$this->existing_teams;
                    continue;
                }

                $teamIdParts = explode('.', $team['id']);

                // create new teams
                $handballnetTeam = new HandballnetTeamsModel();

                $handballnetTeam->pid = $season->id;
                $handballnetTeam->saison = $season->hn_season;
                $handballnetTeam->team_id = $teamIdParts[2];
                $handballnetTeam->provider = $teamIdParts[0];
                $handballnetTeam->verband = $teamIdParts[1];
                $handballnetTeam->handballnet_id = $team['id'];
                $handballnetTeam->liga_shortname = $team['defaultTournament']['acronym'];
                $handballnetTeam->liga_name = $team['defaultTournament']['name'];
                $handballnetTeam->my_team_name = $team['name'];
                $handballnetTeam->is_active = true;
                $handballnetTeam->tstamp = time();

                $handballnetTeam->save();
                
                ++$this->new_teams_created;

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
            'HandballnetTeams Update: '.$this->active_seasons.' aktive Saisons, '.$this->total_teams_checked.' Teams geprüft, '.$this->existing_teams.' existierende Teams, '.$this->new_teams_created.' neue Teams.',
        );
    }
}
