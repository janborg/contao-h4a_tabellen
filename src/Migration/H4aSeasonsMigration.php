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
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;
use Janborg\H4aTabellen\Helper\Helper;
use Janborg\H4aTabellen\Model\H4aSeasonModel;

class H4aSeasonsMigration extends AbstractMigration
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var ContaoFramework
     */
    private $framework;

    public function __construct(Connection $connection, ContaoFramework $framework)
    {
        $this->connection = $connection;
        $this->framework = $framework;
    }

    public function shouldRun(): bool
    {
        $schemaManager = $this->connection->createSchemaManager();

        // If the database table already itself exists we should do nothing
        if ($schemaManager->tablesExist(['tl_h4a_seasons'])) {
            return false;
        }

        $columns = $schemaManager->listTableColumns('tl_calendar');

        return
            isset($columns['h4a_team_id']) &&
            isset($columns['h4a_season'], $columns['my_team_name'])
            ;
    }

    public function run(): MigrationResult
    {
        $this->framework->initialize();

        // neue Tabelle tl_h4a_seasons anlegen
        $this->connection->executeQuery("
        CREATE TABLE tl_h4a_seasons (
            id INT UNSIGNED AUTO_INCREMENT NOT NULL,
            tstamp INT UNSIGNED DEFAULT 0 NOT NULL,
            season VARCHAR(255) DEFAULT '',
            is_current_season CHAR(1) DEFAULT '' NOT NULL,
            PRIMARY KEY(id)
        )
            DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB ROW_FORMAT = DYNAMIC
        ");

        // neues Feld h4a_seasons in der tl_calendar anlegen
        $this->connection->executeQuery('
            ALTER TABLE
                tl_calendar
            ADD
                h4a_seasons blob NULL
        ');
        // alle in Kalendern hinterlegten Saisons finden
        $seasons = $this->connection->fetchAllAssociative('
            SELECT DISTINCT
                h4a_season
            FROM
                tl_calendar
            WHERE
                h4a_imported = 1
            ORDER BY h4a_season ASC
        ');

        // neue Seasons in Tabelle tl_h4a_seasons anlegen
        foreach ($seasons as $season) {
            $objNewSeason = new H4aSeasonModel();
            $objNewSeason->tstamp = time();
            $objNewSeason->season = $season['h4a_season'];
            $objNewSeason->is_current_season = 0;
            $objNewSeason->save();
        }

        //Bei allen tl_calendar mit update_h4a = 1 die Saison, ligaID, teamID, team name in h4a_seasons serialized eintragen
        $objCalendars = CalendarModel::findBy(
            ['tl_calendar.h4a_imported=?'],
            ['1']
        );

        foreach ($objCalendars as $objCalendar) {
            $arrH4aSpielplan = Helper::getJsonSpielplan($objCalendar->h4a_team_ID);

            $objSeason = H4aSeasonModel::findby(['season=?'], [$objCalendar->h4a_season]);

            $h4aSaison[0] = [
                'h4a_saison' => $objSeason->id,
                'h4a_team' => $objCalendar->h4a_team_ID,
                'h4a_liga' => $arrH4aSpielplan['dataList'][0]['gClassID'],
                'my_team_name' => $objCalendar->my_team_name, //$arrH4aSpielplan['dataList'][0]['lvTypeLabelStr], aber ohne "/ " am Anfang
            ];
            $objCalendar->h4a_seasons = serialize($h4aSaison);
            $objCalendar->save();
        }

        return $this->createResult(
            true,
            'Created '.\count($seasons).' new seasons and updated '.\count($objCalendars).' H4a-Calendars.'
        );
    }
}
