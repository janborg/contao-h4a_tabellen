<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

namespace Janborg\H4aTabellen\Cron;

use Contao\CalendarEventsModel;
use Contao\CalendarModel;
use Contao\CoreBundle\Cache\EntityCacheTags;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Monolog\SystemLogger;
use Janborg\H4aTabellen\Helper\Helper;

class UpdateH4aResultsCron
{
    public function __construct(
        private ContaoFramework $framework,
        private EntityCacheTags $entityCacheTags,
        private SystemLogger $systemLogger,
    ) {
        $this->framework->initialize();
    }

    public function updateResults(): void
    {
        $objEvents = CalendarEventsModel::findby(
            ['DATE(FROM_UNIXTIME(startDate)) <= ?', 'h4a_resultComplete != ?', 'gGameID != ?'],
            [date('Y-m-d'), true, ''],
            [
                'eager' => true,
                'having' => 'h4a_season__h4a_ignore = 0',
            ],
        );

        if (null === $objEvents) {
            return;
        }

        foreach ($objEvents as $objEvent) {
            if ($objEvent->startTime > time() || '00:00' === date('H:i', (int) $objEvent->startTime)) {
                continue;
            }

            $objCalendar = CalendarModel::findById($objEvent->pid);

            $h4a_team_ID = Helper::getH4ateamFromH4aSeasons($objCalendar, $objEvent);

            $arrResult = Helper::getJsonSpielplan($h4a_team_ID);

            $games = $arrResult['dataList'];

            if (isset($games[0])) {
                $gameId = array_search($objEvent->gGameID, array_column($games, 'gID'), true);
            } else {
                continue;
            }

            if (
                isset($games[$gameId]['gHomeGoals'], $games[$gameId]['gGuestGoals'])

                && ' ' !== $games[$gameId]['gHomeGoals']
                && ' ' !== $games[$gameId]['gGuestGoals']
            ) {
                $objEvent->gHomeGoals = $games[$gameId]['gHomeGoals'];
                $objEvent->gGuestGoals = $games[$gameId]['gGuestGoals'];
                $objEvent->gHomeGoals_1 = $games[$gameId]['gHomeGoals_1'];
                $objEvent->gGuestGoals_1 = $games[$gameId]['gGuestGoals_1'];
                $objEvent->h4a_resultComplete = true;
                $objEvent->save();

                // log new result
                $this->systemLogger
                    ->info('Ergebnis ('.$games[$gameId]['gHomeGoals'].':'.$games[$gameId]['gGuestGoals'].') für Spiel '.$objEvent->gGameID.' über Handball4all aktualisiert')
                ;

                // Invalidate CacheTag for Event
                $this->entityCacheTags->invalidateTagsFor($objEvent);
            } else {
                $objEvent->h4a_resultComplete = false;

                $this->systemLogger
                    ->info('Ergebnis für Spiel '.$objEvent->title.' ('.$objEvent->gGameID.') über Handball4all geprüft, kein Ergebnis vorhanden')
                ;
            }
        }
    }
}
