<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

namespace Janborg\H4aTabellen\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;
use Janborg\H4aTabellen\HandballnetApiClient;
use Janborg\H4aTabellen\Model\HandballnetClubsModel;
use Janborg\H4aTabellen\Sync\HandballnetSeasonSync;
use Janborg\H4aTabellen\Sync\HandballnetTeamSync;

class CreateHandballnetClubsFromH4aSeasonsMigration extends AbstractMigration
{
    public function __construct(
        private Connection $connection,
        private readonly HandballnetApiClient $handballnetApiClient,
        private readonly HandballnetSeasonSync $seasonSync,
        private readonly HandballnetTeamSync $teamSync,
    ) {
    }

    public function shouldRun(): bool
    {
        if (!$this->tableExists('tl_hn_clubs') || !$this->tableExists('tl_h4a_seasons') || !$this->tableExists('tl_hn_seasons') || !$this->tableExists('tl_hn_teams')) {
            return false;
        }

        $count = $this->connection->fetchOne('SELECT COUNT(*) FROM tl_hn_clubs');

        return 0 === (int) $count;
    }

    public function run(): MigrationResult
    {
        try {
            $seasons = $this->connection->fetchAllAssociative(
                'SELECT DISTINCT club_id, provider, verband FROM tl_h4a_seasons',
            );

            if (empty($seasons)) {
                return new MigrationResult(
                    true,
                    'Keine Daten in tl_h4a_seasons gefunden, keine neuen Clubs angelegt.',
                );
            }

            $clubCount = 0;
            $seasonCount = 0;
            $teamCount = 0;

            foreach ($seasons as $season) {
                $clubId = $season['club_id'];
                $provider = $season['provider'];
                $verband = $season['verband'];

                if (empty($clubId) || empty($provider) || empty($verband)) {
                    continue;
                }

                $handballnetId = $provider.'.'.$verband.'.'.$clubId;

                if (null !== HandballnetClubsModel::findOneByHandballnet_id($handballnetId)) {
                    continue;
                }

                $clubDataRaw = $this->handballnetApiClient->getClubData($handballnetId, false);

                if (!$clubDataRaw) {
                    continue;
                }

                $decoded = json_decode($clubDataRaw, true);

                if (!\is_array($decoded) || !isset($decoded['data']['name'], $decoded['data']['organization'])) {
                    continue;
                }

                $clubData = $decoded['data'];

                $club = new HandballnetClubsModel();
                $club->tstamp = time();
                $club->handballnet_id = $handballnetId;
                $club->name = $clubData['name'];
                $club->acronym = $clubData['acronym'] ?? '';
                $club->org_id = $clubData['organization']['id'] ?? '';
                $club->org_name = $clubData['organization']['name'] ?? '';
                $club->org_acronym = $clubData['organization']['acronym'] ?? '';
                $club->is_active = true;
                $club->save();

                ++$clubCount;

                // Zugehörige Seasons anlegen
                $clubSeasons = $this->seasonSync->syncSeasonsForClub($club, '2025');
                $seasonCount += \count($clubSeasons);

                // Zugehörige Teams je Season anlegen
                foreach ($clubSeasons as $clubSeason) {
                    $this->teamSync->syncTeamsForSeason($clubSeason);
                    ++$teamCount;
                }
            }

            return new MigrationResult(
                true,
                \sprintf(
                    '%d neue Clubs, %d Season(s) und Teams für %d Season(s) angelegt/aktualisiert.',
                    $clubCount,
                    $seasonCount,
                    $teamCount,
                ),
            );
        } catch (\Throwable $e) {
            return new MigrationResult(
                false,
                'Fehler bei der Migration: '.$e->getMessage(),
            );
        }
    }

    private function tableExists(string $tableName): bool
    {
        return $this->connection->createSchemaManager()->tablesExist([$tableName]);
    }
}
