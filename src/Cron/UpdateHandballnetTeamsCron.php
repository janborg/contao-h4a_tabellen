<?php

declare(strict_types=1);

namespace Janborg\H4aTabellen\Cron;

use Contao\CoreBundle\Framework\ContaoFramework;
use Janborg\H4aTabellen\Event\HandballnetTeamCreatedEvent;
use Janborg\H4aTabellen\HandballNet\DataTransferObject\TeamDto;
use Janborg\H4aTabellen\HandballNet\Parser\HandballnetTeamsParser;
use Janborg\H4aTabellen\HandballnetApiClient;
use Janborg\H4aTabellen\Model\HandballnetClubsModel;
use Janborg\H4aTabellen\Model\HandballnetSeasonsModel;
use Janborg\H4aTabellen\Model\HandballnetTeamsModel;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class UpdateHandballnetTeamsCron
{
    public function __construct(
        private ContaoFramework $contaoFramework,
        private HandballnetApiClient $handballnetApiClient,
        private EventDispatcherInterface $eventDispatcher,
        private HandballnetTeamsParser $teamsParser,
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
        $objSeasons = HandballnetSeasonsModel::findBy(
            'is_active',
            true,
            ['order' => 'season_id ASC',
                'eager' => true],
        );

        if (null === $objSeasons) {
            return;
        }

        foreach ($objSeasons as $season) {
            ++$this->active_seasons;

            $club = HandballnetClubsModel::findById($season->pid);

            if (null === $club) {
                $this->contaoCronLogger->error(\sprintf('Club nicht gefunden für Saison %s', $season->id));
                continue;
            }

            try {
                $json = $this->handballnetApiClient->getClubTeamsData($club->handballnet_id, $season->season_id);
            } catch (\Exception $e) {
                $this->contaoCronLogger->error('Fehler beim Abruf über die handballnetApi', [$e->getMessage()]);
                continue;
            }

            $teams = $this->teamsParser->parseClubTeams($json);

            foreach ($teams as $team) {
                $this->processTeam($team, $season);
            }
        }

        $this->contaoCronLogger->info(\sprintf(
            'HandballnetTeams Update: %d aktive Saisons, %d Teams geprüft, %d existierende Teams, %d neue Teams.',
            $this->active_seasons,
            $this->total_teams_checked,
            $this->existing_teams,
            $this->new_teams,
        ));
    }

    private function processTeam(TeamDto $dto, object $season): void
    {
        ++$this->total_teams_checked;
        if (null === $dto->ageGroup) {
            $this->contaoCronLogger->info(\sprintf(
                'Unbekannte Altersgruppe bei Team %s (%s)',
                $dto->id,
                $dto->name,
            ));
        }

        $model = $this->findOrCreate($dto->id);

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

        // Event + Log nur für neue Teams
        if ($model->isNew ?? false) {
            $this->eventDispatcher->dispatch(
                new HandballnetTeamCreatedEvent($model, $season),
            );

            $this->contaoCronLogger->info(\sprintf(
                'Neues Team in der Saison %s erstellt (%s, %s).',
                $season->hn_season,
                $dto->name,
                $dto->tournamentAcronym,
            ));
        }
    }

    private function findOrCreate(string $externalId): HandballnetTeamsModel
    {
        $existing = HandballnetTeamsModel::findOneBy('handballnet_team_id', $externalId);

        if ($existing) {
            ++$this->existing_teams;

            return $existing;
        }

        $model = new HandballnetTeamsModel();
        $model->is_active = true;
        $model->isNew = true;
        ++$this->new_teams;

        return $model;
    }
}
