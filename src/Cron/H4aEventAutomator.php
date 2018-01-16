<?php

namespace Janborg\H4aTabellen\Cron;

use Contao\Backend;
use Contao\System;
use Contao\Database;
use Janborg\H4aTabellen\EventManager;

class H4aEventAutomator extends Backend
{
	public function __construct()
	{
		parent::__construct();
	}

	public function syncCalendars()
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
        while ($calendars->next()) {

			//json File des Teams abrufen und in array umwandeln
			$liga_url = 'https://h4a.it4sport.de/spo/spo-proxy_public.php?cmd=data&lvTypeNext=team&lvIDNext='.$calendars->h4a_team_ID;
			$strSpieleJson = file_get_contents($liga_url);
			$arrResponse = json_decode($strSpieleJson, true);

			$spiele = $arrResponse[0]['dataList'];
			$lvTypeLabelStr = $arrResponse[0]['lvTypeLabelStr'];

			$calendar = $calendars ->id;
			$author = $calendars ->h4aEvents_author;

			if ($lvTypeLabelStr == "/ [error]") {
				System::log('Updateversuch des Kalenders '.$calendar.' abgebrochen, prüfen Sie die Team ID!' , __METHOD__, 'ERROR');
			} else {
				foreach ($spiele as $spiel)
				{
					$eventmanager = new EventManager($spiel);
					$eventmanager->manage($spiel, $calendar, $author);
				}
				System::log('Update des Kalenders '.$calendar.' über Handball4all ausgeführt' , __METHOD__, 'CRON');
			}
		 }
	}

	public function updateEvents()
	{
		$this->syncCalendars();
		$this->redirect($this->getReferer());
	}
}
