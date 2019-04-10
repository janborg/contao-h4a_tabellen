<?php

/*
 * This file is part of contao-h4a_tabellen.
 * (c) Jan Lünborg
 * @license LGPL-3.0-or-later
 */

namespace Janborg\H4aTabellen\Cron;

use Contao\Backend;
use Contao\Database;
use Contao\System;
use Janborg\H4aTabellen\EventManager;
use Janborg\H4aTabellen\Helper\Helper;

class H4aEventAutomator extends Backend
{
    public function __construct()
    {
        parent::__construct();
    }

    public function syncCalendars($calendars)
    {
        while ($calendars->next()) {
            $type = 'team';
            $liga_url = Helper::getURL($type, $calendars->h4a_team_ID);
            $arrResult = Helper::setCachedFile($calendars->h4a_team_ID, $liga_url);

            $spiele = $arrResult[0]['dataList'];
            $lvTypeLabelStr = $arrResult[0]['lvTypeLabelStr'];

            $calendar = $calendars->id;
            $author = $calendars->h4aEvents_author;

            if ('/ [error]' === $lvTypeLabelStr) {
                System::log('Updateversuch des Kalenders '.$calendar.' abgebrochen, prüfen Sie die Team ID!', __METHOD__, 'ERROR');
            } else {
                foreach ($spiele as $spiel) {
                    $eventmanager = new EventManager($spiel);
                    $eventmanager->manage($spiel, $calendar, $author);
                }
                $updated_calendars = $updated_calendars.$calendar.',';
            }
        }
        System::log('Update für Kalender '.\substr($updated_calendars, 0, -1).' über Handball4all ausgeführt', __METHOD__, 'CRON');
    }

    public function updateEvents()
    {
        $database = Database::getInstance();
        $calendars = $database->prepare("
			SELECT
				id, h4a_liga_ID, h4a_team_ID, my_team_name, h4aEvents_author
			FROM
				tl_calendar
			WHERE
				h4a_imported = '1' AND h4a_ignore != '1'
		")->execute();

        $this->syncCalendars($calendars);
        $this->redirect($this->getReferer());
    }

    public function updateArchive()
    {
        $id = [\Input::get('id')];

        $database = Database::getInstance();
        $calendar = $database->prepare('
			SELECT
				id, h4a_liga_ID, h4a_team_ID, my_team_name, h4aEvents_author
			FROM
				tl_calendar
			WHERE
				id = ?
		')->execute($id);

        $this->syncCalendars($calendar);
        $this->redirect($this->getReferer());
    }

    public function updateResults()
    {
      $database = Database::getInstance();
      $gameResults = $database->prepare('
    SELECT DISTINCT
      tl_calendar.id, gGameNo, h4a_liga_ID, h4a_team_ID, my_team_name, h4aEvents_author, gHomeGoals, gGuestGoals, gHomeGoals_1, gGuestGoals_1
    FROM
      tl_calendar
    INNER JOIN
      tl_calendar_events ON tl_calendar.id = tl_calendar_events.pid
    WHERE
      DATE(FROM_UNIXTIME(startdate)) = CURDATE()
    AND
      TIME(FROM_UNIXTIME(starttime)) < CURTIME() + 2
    AND
      gHomeGoals = ""
  ')->execute();

      $this->syncResults($gameResults);


    }
}
