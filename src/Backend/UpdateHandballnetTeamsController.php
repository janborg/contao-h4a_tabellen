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
use Janborg\H4aTabellen\HandballNet\DataTransferObject\TeamDto;
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
        $objSeasons = HandballnetSeasonsModel::findBy(['is_active = ?'], [true]);

        if (null === $objSeasons) {
            Message::addError('Es sind keine aktiven Saisons vorhanden.');
            $this->redirect($this->getReferer());
        }

        $this->active_seasons = $objSeasons->count();

        foreach ($objSeasons as $season) {
            try {
                $data = json_decode(
                    $this->handballnetApiClient->getClubTeamsData($season->handballnet_club_id, $season->season_id),
                    true,
                );
            } catch (\Exception $e) {
                Message::addError($e->getMessage());
                continue;
            }

            foreach ($data['data'] as $team) {
                $this->processTeam($team, $season);
            }
        }

        $this->addSummaryMessages();

        $this->redirect($this->urlGenerator->generate('contao_backend', ['do' => 'handballnet_teams']));
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
            Message::addError($e->getMessage());

            return;
        }

        if (null === $dto->ageGroup) {
            Message::addError(\sprintf(
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
        ++$this->new_teams;

        return $model;
    }

    private function addSummaryMessages(): void
    {
        if ($this->active_seasons > 0) {
            Message::addConfirmation($this->active_seasons.' aktive Saison(s) gefunden und aktualisiert.');
        }

        if ($this->new_teams > 0) {
            Message::addConfirmation($this->new_teams.' neue(s) Team(s) erstellt.');
        }

        if ($this->existing_teams > 0) {
            Message::addInfo($this->existing_teams.' existierende(s) Team(s) aktualisiert.');
        }
    }
}
