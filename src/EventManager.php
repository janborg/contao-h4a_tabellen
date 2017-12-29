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
    public function process(GraphObject $data, GraphObject $image)
    {
        $facebookId = $data->getProperty('id');
        // check if event exists
        if (null !== ($event = $this->checkIfEventExists($gNo))) {
            $this->updateEvent($event, $data, $image);
            return;
        }
        $this->createEvent($data, $image);
        return;
    }

    protected function checkIfEventExists($gGameNo)
    {
        $result = $this->database->prepare('SELECT * FROM tl_calendar_events WHERE gGameNo=? LIMIT 1')
            ->execute($gGameNo)
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
	public function createEvent($h4adata, $calendar)
    {
        $arrDate = explode(".",$h4adata['gDate']);
        $arrTime = explode(":",$h4adata['gTime']);

        $dateDay = mktime(0,0,0,$arrDate[1],$arrDate[0],$arrDate[2]);
		$dateTime = mktime($arrTime[0],$arrTime[1],0,$arrDate[1],$arrDate[0],$arrDate[2]);

        //$timestamps = $this->getTimestamps($data);
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
        // Get the Id of the inserted Event
        /*$insertedEvent = $this->database->prepare('SELECT id FROM tl_calendar_events WHERE facebookEvents_id = ?')
            ->execute($data->getProperty('id'))
        ;
        $eventId = $insertedEvent->next()->id;
        // Insert ContentElement
        $this->database->prepare("
            INSERT INTO tl_content
                (pid, ptable, sorting, tstamp, type, headline, text, floating, sortOrder, perRow, cssID, space, com_order, com_template)
            VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ")
            ->execute(
                $eventId,
                'tl_calendar_events',
                128,
                time(),
                'text',
                serialize(array('unit' => 'h1', 'value' => $data->getProperty('name'))),
                sprintf('<p>%s</p>', nl2br($data->getProperty('description'))),
                'above',
                'ascending',
                4,
                serialize(array('', '')),
                serialize(array('', '')),
                'ascending',
                'com_default'
            )
        ;*/
    }
    protected function updateEvent($eventObj, GraphObject $data, GraphObject $image)
    {
        $timestamps = $this->getTimestamps($data);
        $file = $this->writePicture($data->getProperty('id'), $image);
        $this->database->prepare('UPDATE tl_calendar_events SET title = ?, teaser = ?, singleSRC = ?, addTime = ?, startTime = ?, startDate = ?, endTime = ?, endDate = ?, location = ? WHERE facebookEvents_id = ?')
            ->execute(
                $data->getProperty('name'),
                sprintf('<p>%s</p>', nl2br($data->getProperty('description'))),
                $file->uuid,
                // Timestamps
                $timestamps['addTime'],
                $timestamps['startTime'],
                $timestamps['startDate'],
                $timestamps['endTime'],
                $timestamps['endDate'],
                $data->getProperty('place') ? $data->getProperty('place')->getProperty('name') : '',
                $data->getProperty('id')
            )
        ;
        // Update Text ContentElement
        $this->database->prepare('UPDATE tl_content SET headline = ?, text = ? WHERE type = ? AND pid = ? AND ptable = ?')
            ->execute(
                serialize(array('unit' => 'h1', 'value' => $data->getProperty('name'))),
                sprintf('<p>%s</p>', nl2br($data->getProperty('description'))),
                'text',
                $eventObj->id,
                'tl_calendar_events'
            )
        ;
    }
}
