<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

namespace Janborg\H4aTabellen\Backend;

use Contao\Backend;
use Contao\BackendUser;
use Contao\CalendarEventsModel;
use Contao\CalendarModel;
use Contao\System;
use Janborg\H4aTabellen\Helper\Helper;

class UpdateH4aResultsController extends Backend
{
    public function __construct()
    {
        parent::__construct();
        $this->import(BackendUser::class, 'User');
    }

    public function updateResults(): void
    {
        $objEvents = CalendarEventsModel::findby(
            ['DATE(FROM_UNIXTIME(startDate)) <= ?', 'h4a_resultComplete != ?', 'gGameID != ?'],
            [date('Y-m-d'), true, '']
        );

        if (null === $objEvents) {
            System::getContainer()
                ->get('monolog.logger.contao.general')
                ->info('Es stehen für keine vergangenen Spiele die Ergebnisse aus.')
            ;

            $this->redirect($this->getReferer());

            return;
        }

        foreach ($objEvents as $objEvent) {
            $now = time();

            // Continue, wenn Spiel noch nicht gestartet
            if ($objEvent->startTime > $now || '00:00' === date('H:i', (int) $objEvent->startTime)) {
                continue;
            }

            $objCalendar = CalendarModel::findById($objEvent->pid);

            $h4a_team_ID = Helper::getH4ateamFromH4aSeasons($objCalendar, $objEvent);

            $arrResult = Helper::getJsonSpielplan($h4a_team_ID);

            if (!isset($arrResult['dataList'][0])) {
                System::getContainer()
                    ->get('monolog.logger.contao.general')
                    ->info('Spielplan für Team'.$objCalendar->h4a_team_ID.' ('.$objCalendar->title.') konnte nicht abgerufen werden. Datalist in json ist leer.')
                ;

                continue;
            }

            $games = $arrResult['dataList'];

            $gameId = array_search($objEvent->gGameID, array_column($games, 'gID'), true);

            if (' ' !== $games[$gameId]['gHomeGoals'] && ' ' !== $games[$gameId]['gGuestGoals']) {
                $objEvent->gHomeGoals = $games[$gameId]['gHomeGoals'];
                $objEvent->gGuestGoals = $games[$gameId]['gGuestGoals'];
                $objEvent->gHomeGoals_1 = $games[$gameId]['gHomeGoals_1'];
                $objEvent->gGuestGoals_1 = $games[$gameId]['gGuestGoals_1'];
                $objEvent->h4a_resultComplete = true;
                $objEvent->save();

                System::getContainer()
                    ->get('monolog.logger.contao.general')
                    ->info('Ergebnis ('.$games[$gameId]['gHomeGoals'].':'.$games[$gameId]['gGuestGoals'].' für Spiel '.$objEvent->gGameID.' '.$objEvent->title.' erhalten.')
                ;
            } else {
                $objEvent->h4a_resultComplete = false;

                System::getContainer()
                    ->get('monolog.logger.contao.general')
                    ->info('Ergebnis für Spiel '.$objEvent->gGameID.' '.$objEvent->title.' über Handball4all geprüft, kein Ergebnis vorhanden.')
                ;
            }
        }

        $this->redirect($this->getReferer());
    }
}
