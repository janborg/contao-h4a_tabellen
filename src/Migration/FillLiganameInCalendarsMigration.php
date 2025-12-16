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

class FillLiganameInCalendarsMigration extends AbstractMigration
{
    public function __construct(
        private Connection $connection,
    ) {
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
                // Prüfe ob liga_name leer ist
                if (
                    empty($season['liga_name'])
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
                // Prüfe ob liga_name leer ist
                if (
                    empty($season['liga_name'])
                ) {
                    // Setze liga_name
                    $seasons[$key]['liga_name'] = $season['liga_name'];
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
            \sprintf('Erfolgreich liga_name für %d Kalender aktualisiert.', $updatedCount),
        );
    }
}
