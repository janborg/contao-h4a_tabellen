<?php

namespace Janborg\H4aTabellen;

use Contao\Database;
use GuzzleHttp\Client;

class EventManager
{
    protected $guzzleClient;
    protected $database;
    protected $config;

	public function __construct(array $config)
    {
        $this->guzzleClient = new Client();
        $this->database = Database::getInstance();
        $this->config = $config;
    }
    public function manage($h4adata, $calendar)
    {
        $EventGameNo = $h4adata['gNo'];
        // check if event exists
        if (null !== ($event = $this->checkIfEventExists($EventGameNo))) {
            $this->updateEvent($h4adata, $calendar);
            return;
        }
        $this->createEvent($h4adata, $calendar);
        return;
    }

    protected function checkIfEventExists($EventGameNo)
    {
        $result = $this->database->prepare('SELECT * FROM tl_calendar_events WHERE gGameNo=? LIMIT 1')
            ->execute($EventGameNo)
        ;
        if ($result->numRows > 0) {
            return $result->first();
        }
        return null;
    }
    /**
     * @param $h4adata
     * @param $calendar
     */
	protected function createEvent($h4adata, $calendar)
    {
        $arrDate = explode(".",$h4adata['gDate']);
        $arrTime = explode(":",$h4adata['gTime']);

        $dateDay = mktime(0,0,0,$arrDate[1],$arrDate[0],$arrDate[2]);
		$dateTime = mktime($arrTime[0],$arrTime[1],0,$arrDate[1],$arrDate[0],$arrDate[2]);

        $this->database->prepare("
            INSERT INTO tl_calendar_events
                (pid, tstamp, title, alias, author, addTime, startTime, endTime, startDate, location, gGameNo, gClassName, gHomeTeam, gGuestTeam, gResult, gHalfResult, published)
            VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")
            ->execute(
                $calendar,//3,//$this->config['calendar'],
                time(),
                $h4adata['gClassSname'].": ".$h4adata['gHomeTeam']." - ".$h4adata['gGuestTeam'],
				standardize(\StringUtil::restoreBasicEntities($h4adata['gClassSname']."_".$h4adata['gHomeTeam']."_".$h4adata['gGuestTeam']."_".$h4adata['gNo'])),

				//Author
                1,
                //Timestamps
                1,
                $dateTime,
				$dateTime,
                $dateDay,
                $h4adata['gGymnasiumName'],

				$h4adata['gNo'],
				$h4adata['gClassSname'],
				$h4adata['gHomeTeam'],
				$h4adata['gGuestTeam'],
				$h4adata['gHomeGoals'].":".$h4adata['gGuestGoals'],
				$h4adata['gHomeGoals_1'].":".$h4adata['gGuestGoals_1'],

                true
            )
        ;
    }
    protected function updateEvent($h4adata, $calendar)
    {
        $arrDate = explode(".",$h4adata['gDate']);
        $arrTime = explode(":",$h4adata['gTime']);

        $dateDay = mktime(0,0,0,$arrDate[1],$arrDate[0],$arrDate[2]);
		$dateTime = mktime($arrTime[0],$arrTime[1],0,$arrDate[1],$arrDate[0],$arrDate[2]);

        $this->database->prepare('UPDATE tl_calendar_events SET addTime = ?, startTime = ?, endTime = ?, startDate = ?,  location = ?, gResult = ?, gHalfResult = ? WHERE gGameNo = ?')
            ->execute(
                //Timestamps
                1,
                $dateTime,
				$dateTime,
                $dateDay,
                $h4adata['gGymnasiumName'],
                $h4adata['gHomeGoals'].":".$h4adata['gGuestGoals'],
                $h4adata['gHomeGoals_1'].":".$h4adata['gGuestGoals_1'],
                $h4adata['gNo']
            )
        ;
    }
}
