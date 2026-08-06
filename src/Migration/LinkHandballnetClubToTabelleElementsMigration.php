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

use Contao\ContentModel;
use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;
use Janborg\H4aTabellen\Model\HandballnetSeasonsModel;
use Janborg\H4aTabellen\Model\HandballnetTeamsModel;

class LinkHandballnetClubToTabelleElementsMigration extends AbstractMigration
{
    private const CE_TYPE = 'handballnet_tabelle';

    public function __construct(private Connection $connection)
    {
    }

    public function getName(): string
    {
        return 'Handballnet: Verknüpfe handballnet_tabelle Content Elements über handballnet_tournament_id';
    }

    public function shouldRun(): bool
    {
        if (!$this->tableExists('tl_calendar') || !$this->tableExists('tl_hn_clubs') || !$this->tableExists('tl_hn_seasons') || !$this->tableExists('tl_hn_teams')) {
            return false;
        }

        $columns = $this->connection->createSchemaManager()->listTableColumns('tl_content');

        foreach (['type', 'handballnet_club', 'handballnet_season', 'handballnet_tournament_id'] as $required) {
            if (!isset($columns[$required])) {
                return false;
            }
        }

        $count = $this->connection->fetchOne(
            "SELECT COUNT(*) FROM tl_content
             WHERE type = ?
               AND COALESCE(handballnet_club, '') = ''
               AND COALESCE(handballnet_tournament_id, '') != ''",
            [self::CE_TYPE],
        );

        return (int) $count > 0;
    }

    public function run(): MigrationResult
    {
        $rows = $this->connection->fetchAllAssociative(
            "SELECT id FROM tl_content
             WHERE type = ?
               AND COALESCE(handballnet_club, '') = ''
               AND COALESCE(handballnet_tournament_id, '') != ''",
            [self::CE_TYPE],
        );

        $linked = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            $contentElement = ContentModel::findById($row['id']);

            if (null === $contentElement) {
                continue;
            }

            $team = HandballnetTeamsModel::findOneByHandballnet_tournament_id($contentElement->handballnet_tournament_id);

            if (null === $team) {
                ++$skipped;
                continue;
            }

            $season = HandballnetSeasonsModel::findById($team->pid);

            if (null === $season) {
                ++$skipped;
                continue;
            }

            $contentElement->handballnet_club = $season->handballnet_club_id;
            $contentElement->handballnet_season = $season->id;
            $contentElement->save();

            ++$linked;
        }

        return new MigrationResult(
            true,
            \sprintf(
                '%d Tabelle-Element(e) verknüpft, %d übersprungen (Team/Turnier noch nicht synchronisiert).',
                $linked,
                $skipped,
            ),
        );
    }

    private function tableExists(string $tableName): bool
    {
        return $this->connection->createSchemaManager()->tablesExist([$tableName]);
    }
}
