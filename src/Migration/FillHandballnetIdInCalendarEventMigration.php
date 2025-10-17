<?php

declare(strict_types=1);

namespace Janborg\H4aTabellen\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;

class FillHandballnetIdInCalendarEventMigration extends AbstractMigration
{
    public function __construct(
        private Connection $connection
        )
    {
        
    }

    public function shouldRun(): bool
    {
        $schemaManager = $this->connection->createSchemaManager();

        // Prüfe ob die Tabelle existiert
        if (!$schemaManager->tablesExist(['tl_calendar_events'])) {
            return false;
        }

        $columns = $schemaManager->listTableColumns('tl_calendar_events');
        $columnNames = array_keys($columns);

        // Prüfe ob alle benötigten Felder existieren
        $requiredFields = ['handballnet_id', 'gGameID', 'provider', 'verband'];
        foreach ($requiredFields as $field) {
            if (!in_array(strtolower($field), array_map('strtolower', $columnNames))) {
                return false;
            }
        }

        // Prüfe ob es Datensätze gibt, die aktualisiert werden müssen
        $count = $this->connection->fetchOne(
            "SELECT COUNT(*) FROM tl_calendar_events 
             WHERE (handballnet_id IS NULL OR handballnet_id = '') 
             AND gGameID IS NOT NULL AND gGameID != '' 
             AND provider IS NOT NULL AND provider != '' 
             AND verband IS NOT NULL AND verband != ''"
        );

        return $count > 0;
    }

    public function run(): MigrationResult
    {
        // Aktualisiere die Datensätze
        $affectedRows = $this->connection->executeStatement(
            "UPDATE tl_calendar_events 
             SET handballnet_id = CONCAT(provider, '.', verband, '.', gGameID)
             WHERE (handballnet_id IS NULL OR handballnet_id = '') 
             AND gGameID IS NOT NULL AND gGameID != '' 
             AND provider IS NOT NULL AND provider != '' 
             AND verband IS NOT NULL AND verband != ''"
        );

        return $this->createResult(
            true,
            sprintf('Erfolgreich %d Datensätze aktualisiert.', $affectedRows)
        );
    }
}