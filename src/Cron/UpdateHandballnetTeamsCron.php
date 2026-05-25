<?php

declare(strict_types=1);

namespace Janborg\H4aTabellen\Cron;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCronJob;
use Contao\CoreBundle\Framework\ContaoFramework;
use Janborg\H4aTabellen\Event\HandballnetTeamCreatedEvent;
use Janborg\H4aTabellen\HandballNet\DataTransferObject\TeamDto;
use Janborg\H4aTabellen\HandballnetApiClient;
use Janborg\H4aTabellen\Model\HandballnetSeasonsModel;
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
        $objSeasons = HandballnetSeasonsModel::findBy(
            ['is_active=?'],
            [true],
            ['order' => 'season_id ASC'],
        );

        if (null === $objSeasons) {
            return;
        }

        foreach ($objSeasons as $season) {
            ++$this->active_seasons;

            try {
                $data = json_decode(
                    $this->handballnetApiClient->getClubTeamsData($season->handballnet_club_id, $season->season_id),
                    true,
                );
            } catch (\Exception $e) {
                $this->contaoCronLogger->error('Fehler beim Abruf über die handballnetApi', [$e->getMessage()]);
                continue;
            }

            foreach ($data['data'] as $teamData) {
                ++$this->total_teams_checked;
                $this->processTeam($teamData, $season);
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

    /**
     * Undocumented function.
     *
     * @param array<mixed> $teamData
     */
    private function processTeam(array $teamData, object $season): void
    {
        try {
            $dto = TeamDto::fromArray($teamData);
        } catch (\InvalidArgumentException $e) {
            $this->contaoCronLogger->info($e->getMessage());

            return;
        }

        if (null === $dto->ageGroup) {
            $this->contaoCronLogger->info(\sprintf(
                'Unbekannte Altersgruppe "%s" bei Team %s (%s)',
                $teamData['defaultTournament']['ageGroup'] ?? 'leer',
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
