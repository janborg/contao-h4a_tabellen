<?php

declare(strict_types=1);

namespace Janborg\H4aTabellen\Cron;

use Contao\CoreBundle\Framework\ContaoFramework;
use Janborg\H4aTabellen\HandballnetApiClient;
use Janborg\H4aTabellen\Model\HandballnetClubsModel;
use Janborg\H4aTabellen\Model\HandballnetSeasonsModel;
use Psr\Log\LoggerInterface;

class UpdateHandballnetSeasonsCron
{
    public function __construct(
        private ContaoFramework $contaoFramework,
        private HandballnetApiClient $handballnetApiClient,
        private readonly LoggerInterface|null $contaoCronLogger,
        private int $active_clubs = 0,
        private int $existing_seasons = 0,
        private int $new_seasons = 0,
    ) {
        $this->contaoFramework->initialize();
    }

    public function updateHandballnetSeasons(): void
    {
        $objClubs = HandballnetClubsModel::findBy(
            ['is_active = ?'],
            [true],
        );

        if (null === $objClubs) {
            return;
        }

        foreach ($objClubs as $club) {
            ++$this->active_clubs;

            try {
                $data = json_decode($this->handballnetApiClient->getClubTeamsData($club->handballnet_id, (string) date('Y')), true);
            } catch (\Exception $e) {
                $this->contaoCronLogger->error('Fehler beim Abruf über die handballnetApi', [$e->getMessage()]);
                continue;
            }

            $clubSeasons = $data['meta']['facets']['0']['values'] ?? [];

            foreach ($clubSeasons as $season) {
                $this->processSeason($season, $club);
            }
        }

        $this->contaoCronLogger->info(\sprintf(
            'HandballnetSeasons Update: %d aktive Clubs, %d Saisons geprüft, %d existierende Saisons, %d neue Saisons.',
            $this->active_clubs,
            $this->existing_seasons + $this->new_seasons,
            $this->existing_seasons,
            $this->new_seasons,
        ));
    }

    /**
     * @param array<string, mixed> $season
     */
    private function processSeason(array $season, object $club): void
    {
        $model = $this->findOrCreate((string) $club->id, (string) $season['id']);

        $model->season_id = $season['id'];
        $model->season_name = $season['name'];
        $model->handballnet_club_id = $club->id;
        $model->club_name = $club->name;

        $model->tstamp = time();
        $model->save();
    }

    private function findOrCreate(string $clubId, string $seasonId): HandballnetSeasonsModel
    {
        $existing = HandballnetSeasonsModel::findOneBy(
            ['handballnet_club_id=?', 'season_id=?'],
            [$clubId, $seasonId],
        );

        if ($existing) {
            ++$this->existing_seasons;

            return $existing;
        }

        $model = new HandballnetSeasonsModel();
        $model->is_active = true;
        $model->pid = $clubId;
        ++$this->new_seasons;

        return $model;
    }
}
