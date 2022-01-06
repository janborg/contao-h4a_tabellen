<?php

namespace Janborg\H4aTabellen\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;
use Contao\CalendarModel;
use Janborg\H4aTabellen\Helper\Helper;
use Janborg\H4aTabellen\Model\H4aSeasonModel;

class H4aSeasonsMigration extends AbstractMigration
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function shouldRun(): bool
    {
        $schemaManager = $this->connection->getSchemaManager();

        // If the database table itself does not exist we should do nothing
        if (!$schemaManager->tablesExist(['tl_h4a_seasons'])) {
            return false;
        }

        $columns = $schemaManager->listTableColumns('tl_calendar');

        return 
	        isset($columns['h4a_team_ID']) &&
	        isset($columns['h4a_season']) &&
	        !isset($columns['my_team_name']);
    }

    public function run(): MigrationResult
    {

        // neue Tabelle tl_h4a_seasons anlegen
        $this->connection->executeQuery("
            CREATE TABLE
                tl_h4a_seasons (
                    id int(10) unsigned NOT NULL auto_increment,
                    tstamp int(10) unsigned NOT NULL default '0',
                    season varchar(255) NULL default '',
                    is_current_season char(1) NOT NULL default ''
                )
        ");

        // neues Feld h4a_seasons in der tl_calendar anlegen
        $this->connection->executeQuery("
            ALTER TABLE
                tl_calendar
            ADD
                h4a_seasons blob NULL
        ");
        // alle in Kalendern hinterlegten Saisons finden
        $seasons = $this->connection->executeQuery("
            SELECT DISTINCT
                h4a_season 
            FROM 
                tl_calendar
            WHERE
                h4a_imported = 1
        ");
        
        // neue Seasons in Tabelle tl_h4a_seasons anlegen
        foreach ($seasons as $season) {
            $objSeason = new H4aSeasonModel();
            $objSeason->h4a_saison = $season->h4a_season;
            $objSeason->is_current_season = 0;
            $objSeason->save();
        };


        //Bei allen tl_calendar mit update_h4a = 1 die Saison, ligaID, teamID, team name in h4a_seasons serialized eintragen
        $objCalendars = CalendarModel::findBy(
            ['tl_calendar.h4a_imported=?'],
            ['1']
        );
        foreach ($objCalendars as $objCalendar) {
            $arrH4aSpielplan = Helper::getJsonSpielplan($objCalendar->h4a_team_ID);
            
            $h4aSaison = [
                'h4a_saison' => H4aSeasonModel::findby('season=?', $objCalendar->h4a_season),
                'h4a_team' => $objCalendar->h4a_team_ID,
                'h4a_liga' => $arrH4aSpielplan['dataList'][0]['gClassID'],
                'my_team_name' => $objCalendar->my_team_name,
            ];
            $objCalendar->h4a_seasons = serialize($h4aSaison);
            $objCalendar->save();
        };
        


        return $this->createResult( 
            true, 
            'Created new seasons and updated H4a calendars.'
        );
    }
}
