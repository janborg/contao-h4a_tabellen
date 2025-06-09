<?php

declare(strict_types=1);

namespace Janborg\H4aTabellen\Cron;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCronJob;
use Contao\CoreBundle\Framework\ContaoFramework;
use Janborg\H4aTabellen\Crawler\TeamsCrawler;
use Janborg\H4aTabellen\Event\HandballnetTeamCreatedEvent;
use Janborg\H4aTabellen\Model\H4aSeasonModel;
use Janborg\H4aTabellen\Model\HandballnetTeamsModel;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsCronJob('daily', method: 'updateHandballnetTeams')]
class UpdateHandballnetTeamsCron
{
    public function __construct(
        private ContaoFramework $contaoFramework,
        private TeamsCrawler $teamsCrawler,
        private EventDispatcherInterface $eventDispatcher,
        private readonly LoggerInterface|null $contaoCronLogger,
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
            $this->teamsCrawler->setClubID($season->club_id);
            $this->teamsCrawler->setProvider($season->provider);
            $this->teamsCrawler->setVerbandName($season->verband);
            $this->teamsCrawler->setSeason($season->hn_season);

            $teams = $this->teamsCrawler->getAllTeams();

            if (empty($teams) || !\is_array($teams)) {
                continue;
            }

            foreach ($teams as $team) {
                $handballnetTeam = HandballnetTeamsModel::findBy(
                    ['team_id=?', 'liga_id=?', 'liga_shortname=?'],
                    [$team->team_id, $team->liga_id, $team->liga_short_name],
                );

                // Continue, if team already exists
                if ($handballnetTeam) {
                    continue;
                }

                // skip teams without team_id or liga_id
                if (empty($team->team_id) || empty($team->liga_id)) {
                    continue;
                }

                // create new team
                $handballnetTeam = new HandballnetTeamsModel();

                $handballnetTeam->pid = $season->id;
                $handballnetTeam->saison = $season->hn_season;
                $handballnetTeam->team_id = $team->team_id;
                $handballnetTeam->liga_id = $team->liga_id;
                $handballnetTeam->provider = $team->provider->toString();
                $handballnetTeam->verband = $team->verband->toString();
                $handballnetTeam->liga_shortname = $team->liga_short_name;
                $handballnetTeam->liga_name = $team->liga_name;
                $handballnetTeam->my_team_name = $team->team_name;
                $handballnetTeam->is_active = true;
                $handballnetTeam->tstamp = time();

                $handballnetTeam->save();

                // Dispatch Event for every created Event
                $this->eventDispatcher->dispatch(
                    new HandballnetTeamCreatedEvent($handballnetTeam, $season),
                );

                // Log created Event
                $this->contaoCronLogger->info(
                    'Neues Team in der Saison '.$season->hn_season.' erstellt ('.$team->team_name.', '.$team->liga_name.').',
                );
            }
        }
    }
}
