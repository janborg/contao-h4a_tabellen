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

class FillHandballnetIdInCalendarsMigration extends AbstractMigration
{
    public function __construct(private Connection $connection)
    {
    }

    public function shouldRun(): bool
    {
        $schemaManager = $this->connection->createSchemaManager();

        // Prüfe ob die Tabelle existiert
        if (!$schemaManager->tablesExist(['tl_calendar'])) {
            return false;
        }

        $columns = $schemaManager->listTableColumns('tl_calendar');
        $columnNames = array_keys($columns);

        // Prüfe ob das h4a_seasons Feld existiert
        if (!\in_array('h4a_seasons', array_map('strtolower', $columnNames), true)) {
            return false;
        }

        // Prüfe ob es Datensätze gibt, die aktualisiert werden müssen
        $calendars = $this->connection->fetchAllAssociative(
            'SELECT id, h4a_seasons FROM tl_calendar WHERE h4a_seasons IS NOT NULL',
        );

        foreach ($calendars as $calendar) {
            $seasons = unserialize($calendar['h4a_seasons']);

            if (!\is_array($seasons)) {
                continue;
            }

            foreach ($seasons as $season) {
                // Prüfe ob handballnet_id leer ist und die anderen Felder gesetzt sind
                if (
                    empty($season['handballnet_id'])
                    && !empty($season['provider'])
                    && !empty($season['verband'])
                    && !empty($season['h4a_team'])
                ) {
                    return true;
                }
            }
        }

        return false;
    }

    public function run(): MigrationResult
    {
        $calendars = $this->connection->fetchAllAssociative(
            'SELECT id, h4a_seasons FROM tl_calendar WHERE h4a_seasons IS NOT NULL',
        );

        $updatedCount = 0;

        foreach ($calendars as $calendar) {
            $seasons = unserialize($calendar['h4a_seasons']);

            if (!\is_array($seasons)) {
                continue;
            }

            $needsUpdate = false;

            foreach ($seasons as $key => $season) {
                // Prüfe ob handballnet_id leer ist und die anderen Felder gesetzt sind
                if (
                    empty($season['handballnet_id'])
                    && !empty($season['provider'])
                    && !empty($season['verband'])
                    && !empty($season['h4a_team'])
                ) {
                    // Setze handballnet_id
                    $seasons[$key]['handballnet_id'] = $season['provider'].'.'.$season['verband'].'.'.$season['h4a_team'];
                    $needsUpdate = true;
                }
            }

            // Speichere nur wenn Änderungen vorgenommen wurden
            if ($needsUpdate) {
                $this->connection->update(
                    'tl_calendar',
                    ['h4a_seasons' => serialize($seasons)],
                    ['id' => $calendar['id']],
                );
                ++$updatedCount;
            }
        }

        return $this->createResult(
            true,
            \sprintf('Erfolgreich %d Kalender aktualisiert.', $updatedCount),
        );
    }
}
