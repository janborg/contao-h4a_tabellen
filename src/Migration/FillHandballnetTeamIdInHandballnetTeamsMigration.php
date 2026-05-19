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

class FillHandballnetTeamIdInHandballnetTeamsMigration extends AbstractMigration
{
    public function __construct(private Connection $connection)
    {
    }

    public function shouldRun(): bool
    {
        $schemaManager = $this->connection->createSchemaManager();

        // Prüfe ob die Tabelle existiert
        if (!$schemaManager->tablesExist(['tl_hn_teams'])) {
            return false;
        }

        $columns = $schemaManager->listTableColumns('tl_hn_teams');
        $columnNames = array_keys($columns);

        // Prüfe ob alle benötigten Felder existieren
        $requiredFields = ['handballnet_team_id', 'team_id', 'provider', 'verband'];

        foreach ($requiredFields as $field) {
            if (!\in_array(strtolower($field), array_map('strtolower', $columnNames), true)) {
                return false;
            }
        }

        // Prüfe ob es Datensätze gibt, die aktualisiert werden müssen
        $count = $this->connection->fetchOne(
            "SELECT COUNT(*) FROM tl_hn_teams
             WHERE (handballnet_team_id IS NULL OR handballnet_team_id = '')
             AND team_id IS NOT NULL AND team_id != ''
             AND provider IS NOT NULL AND provider != ''
             AND verband IS NOT NULL AND verband != ''",
        );

        return $count > 0;
    }

    public function run(): MigrationResult
    {
        // Aktualisiere die Datensätze
        $affectedRows = $this->connection->executeStatement(
            "UPDATE tl_hn_teams
             SET handballnet_team_id = CONCAT(provider, '.', verband, '.', team_id)
             WHERE (handballnet_team_id IS NULL OR handballnet_team_id = '')
             AND team_id IS NOT NULL AND team_id != ''
             AND provider IS NOT NULL AND provider != ''
             AND verband IS NOT NULL AND verband != ''",
        );

        return $this->createResult(
            true,
            \sprintf('Erfolgreich %d Datensätze aktualisiert.', $affectedRows),
        );
    }
}
