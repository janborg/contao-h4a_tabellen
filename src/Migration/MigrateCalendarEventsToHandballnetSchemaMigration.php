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

use Contao\CalendarEventsModel;
use Contao\CalendarModel;
use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;
use Janborg\H4aTabellen\HandballNet\HandballnetEventAutomator;

class MigrateCalendarEventsToHandballnetSchemaMigration extends AbstractMigration
{
    public function __construct(
        private Connection $connection,
        private HandballnetEventAutomator $handballnetEventAutomator,
    ) {
    }

    public function getName(): string
    {
        return 'Handballnet: Migriert tl_calendar_events Altfelder auf das neue Handballnet-Schema und synct aktuelle Season via API';
    }

    public function shouldRun(): bool
    {
        $requiredOld = ['handballnet_id', 'h4a_resultComplete', 'gHomeGoals', 'gGuestGoals', 'gHomeGoals_1', 'gGuestGoals_1', 'gHomeTeam', 'gGuestTeam'];
        $requiredNew = ['handballnet_game_id', 'hn_resultComplete', 'homeGoals', 'awayGoals', 'homeGoalsHalf', 'awayGoalsHalf', 'homeTeam_name', 'awayTeam_name'];
        $requiredAll = array_merge($requiredOld, $requiredNew);

        if (!$this->tableExists('tl_calendar_events')) {
            return false;
        }

        $columns = $this->connection->createSchemaManager()->listTableColumns('tl_calendar_events');

        foreach ($requiredAll as $required) {
            if (!isset($columns[$required])) {
                return false;
            }
        }

        $count = $this->connection->fetchOne(
            "SELECT COUNT(*) FROM tl_calendar_events
             WHERE COALESCE(handballnet_id, '') != ''
               AND COALESCE(handballnet_game_id, '') = ''",
        );

        return (int) $count > 0;
    }

    public function run(): MigrationResult
    {
        $migrated = $this->migrateRawFields();
        $syncedCalendars = $this->syncCurrentSeasonViaApi();

        return new MigrationResult(
            true,
            \sprintf(
                '%d Event(s) auf das neue Schema migriert (Tore, Teamnamen, Ergebnis-Flag). '.
                '%d Kalender zusätzlich über die Handballnet-API synchronisiert – dabei wurden '.
                'Season, Tournament, Team-IDs und Status für die AKTUELLE Season vollständig '.
                'nachgezogen. Events aus vergangenen Seasons bleiben ohne Season-/Tournament-Zuordnung, '.
                'da sie nicht mehr im aktuellen Spielplan des Teams enthalten sind. '.
                'Hinweis: gGameID, gGameNo, gComment, provider, verband sowie alle Hallendaten '.
                '(gGymnasium*) wurden NICHT übernommen und gehen mit dem Schema-Wechsel verloren.',
                $migrated,
                $syncedCalendars,
            ),
        );
    }

    private function migrateRawFields(): int
    {
        $rows = $this->connection->fetchAllAssociative(
            "SELECT id FROM tl_calendar_events
             WHERE COALESCE(handballnet_id, '') != ''
               AND COALESCE(handballnet_game_id, '') = ''",
        );

        $migrated = 0;

        foreach ($rows as $row) {
            $event = CalendarEventsModel::findById($row['id']);

            if (null === $event) {
                continue;
            }

            $event->handballnet_game_id = $event->handballnet_id;
            $event->homeTeam_name = $event->gHomeTeam ?? '';
            $event->awayTeam_name = $event->gGuestTeam ?? '';
            $event->homeGoals = $event->gHomeGoals ?? '';
            $event->awayGoals = $event->gGuestGoals ?? '';
            $event->homeGoalsHalf = $event->gHomeGoals_1 ?? '';
            $event->awayGoalsHalf = $event->gGuestGoals_1 ?? '';
            $event->hn_resultComplete = '1' === ($event->h4a_resultComplete ?? '');
            $event->save();

            ++$migrated;
        }

        return $migrated;
    }

    private function syncCurrentSeasonViaApi(): int
    {
        $objCalendars = CalendarModel::findBy(
            ['COALESCE(handballnet_imported, 0) = ?'],
            [true],
        );

        if (null === $objCalendars) {
            return 0;
        }

        $count = 0;

        foreach ($objCalendars as $objCalendar) {
            try {
                $this->handballnetEventAutomator->syncCalendars($objCalendar);
                ++$count;
            } catch (\Throwable $e) {
                // Ein fehlerhafter Kalender soll die übrigen nicht blockieren.
                continue;
            }
        }

        return $count;
    }

    private function tableExists(string $tableName): bool
    {
        return $this->connection->createSchemaManager()->tablesExist([$tableName]);
    }
}
