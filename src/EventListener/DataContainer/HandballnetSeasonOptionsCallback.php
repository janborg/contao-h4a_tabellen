<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

namespace Janborg\H4aTabellen\EventListener\DataContainer;

use Contao\CalendarModel;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Contao\Input;
use Janborg\H4aTabellen\Model\HandballnetSeasonsModel;

class HandballnetSeasonOptionsCallback
{
    /**
     * Callback to get Handballnet Tournament IDs.
     *
     * @return array<int, string>
     */
    #[AsCallback(table: 'tl_calendar', target: 'fields.handballnet_season.options')]
    #[AsCallback(table: 'tl_calendar_events', target: 'fields.handballnet_season.options')]
    #[AsCallback(table: 'tl_content', target: 'fields.handballnet_season.options')]
    public function getHandballnetSeasonOptions(DataContainer $dc): array
    {
        $options = [];

        switch ($dc->table) {
            case "tl_calendar":
                $seasons = HandballnetSeasonsModel::findBy(
                    ['pid=?', 'is_active=?'],
                    [$dc->activeRecord->handballnet_club, true],
                    ['order' => 'season_name DESC'],
                );

                break;

            case "tl_calendar_events":

                $calendarId = $dc->activeRecord->pid ?? null;

                if (null === $calendarId) {
                    $calendarId = Input::get('id');
                }
                $objCalendar = CalendarModel::findById($calendarId);

                $seasons = HandballnetSeasonsModel::findBy(
                    ['pid=?'],
                    [$objCalendar->handballnet_club],
                    ['order' => 'season_name DESC'],
                );
                break;

            case "tl_content":
                $seasons = HandballnetSeasonsModel::findBy(
                    ['pid=?'],
                    [$dc->activeRecord->handballnet_club],
                    ['order' => 'season_name DESC'],
                );
                break;

            default:
                $seasons = HandballnetSeasonsModel::findAll(
                    ['order' => 'season_name DESC'],
                );
        }


        if (null === $seasons) {
            return $options;
        }

        foreach ($seasons as $season) {
            $options[$season->id] = \sprintf(
                '%s - %s',
                $season->season_name,
                $season->club_name,
            );
        }

        return $options;
    }
}
