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

use Contao\CalendarModel;
use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Janborg\H4aTabellen\Model\HandballnetSeasonsModel;
use Janborg\H4aTabellen\Model\HandballnetTeamsModel;

class LinkHandballnetTeamsToCalendarsMigration extends AbstractMigration
{
    public function __construct(private Connection $connection)
    {
    }

    public function getName(): string
    {
        return 'Handballnet: Verknüpfe migrierte h4a-Kalender mit Team/Season/Club';
    }

    public function shouldRun(): bool
    {
        if (!$this->tableExists('tl_calendar') || !$this->tableExists('tl_h4a_seasons') || !$this->tableExists('tl_hn_seasons') || !$this->tableExists('tl_hn_teams')) {
            return false;
        }

        $columns = $this->connection->createSchemaManager()->listTableColumns('tl_calendar');

        foreach (['h4a_imported', 'h4a_seasons', 'handballnet_season', 'handballnet_team_id'] as $required) {
            if (!isset($columns[$required])) {
                return false;
            }
        }

        $count = $this->connection->fetchOne(
            "SELECT COUNT(*) FROM tl_calendar
             WHERE h4a_imported = '1'
               AND h4a_seasons IS NOT NULL
               AND h4a_seasons != ''
               AND (handballnet_season = '' OR handballnet_team_id = '')",
        );

        return (int) $count > 0;
    }

    public function run(): MigrationResult
    {
        $rows = $this->connection->fetchAllAssociative(
            "SELECT id FROM tl_calendar
             WHERE h4a_imported = '1'
               AND h4a_seasons IS NOT NULL
               AND h4a_seasons != ''
               AND (handballnet_season = '' OR handballnet_team_id = '')",
        );

        $linked = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            $calendar = CalendarModel::findById($row['id']);

            if (null === $calendar) {
                continue;
            }

            $seasons = StringUtil::deserialize($calendar->h4a_seasons, true);

            if (empty($seasons)) {
                ++$skipped;
                continue;
            }

            $latest = $this->findLatestSeason($seasons);
            $externalTeamId = trim((string) ($latest['handballnet_id'] ?? ''));

            if ('' === $externalTeamId) {
                ++$skipped;
                continue;
            }

            $team = HandballnetTeamsModel::findOneByHandballnet_team_id($externalTeamId);

            if (null === $team) {
                // Team wurde noch nicht über tl_hn_teams synchronisiert. Da die Felder leer
                // bleiben, greift shouldRun() beim nächsten Lauf erneut.
                ++$skipped;
                continue;
            }

            $season = HandballnetSeasonsModel::findById($team->pid);

            if (null === $season) {
                ++$skipped;
                continue;
            }

            $calendar->handballnet_club = $season->handballnet_club_id;
            $calendar->handballnet_season = $season->id;
            $calendar->handballnet_team_id = $team->handballnet_team_id;
            $calendar->handballnet_imported = true;
            $calendar->save();

            ++$linked;
        }

        return new MigrationResult(
            true,
            \sprintf(
                '%d Kalender verknüpft, %d übersprungen (Team/Season noch nicht synchronisiert oder keine Daten in h4a_seasons).',
                $linked,
                $skipped,
            ),
        );
    }

    /**
     * @param array<int, array<string, mixed>> $seasons
     *
     * @return array<string, mixed>
     */
    private function findLatestSeason(array $seasons): array
    {
        usort(
            $seasons,
            static fn (array $a, array $b): int => strnatcmp(
                (string) ($b['h4a_saison'] ?? ''),
                (string) ($a['h4a_saison'] ?? ''),
            ),
        );

        return $seasons[0];
    }

    private function tableExists(string $tableName): bool
    {
        return $this->connection->createSchemaManager()->tablesExist([$tableName]);
    }
}
