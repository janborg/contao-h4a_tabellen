<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

use Janborg\H4aTabellen\Backend\UpdateH4aCalendarsController;
use Janborg\H4aTabellen\Backend\UpdateH4aEventsController;
use Janborg\H4aTabellen\Backend\UpdateH4aResultsController;
use Janborg\H4aTabellen\Backend\UpdateHandballnetClubSeasonsController;
use Janborg\H4aTabellen\Backend\UpdateHandballnetTeamsController;
use Janborg\H4aTabellen\Model\HandballnetClubsModel;
use Janborg\H4aTabellen\Model\HandballnetSeasonsModel;
use Janborg\H4aTabellen\Model\HandballnetTeamsModel;

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

$GLOBALS['BE_MOD']['content']['calendar']['h4a_update_events'] = [UpdateH4aEventsController::class, 'updateEvents'];
$GLOBALS['BE_MOD']['content']['calendar']['h4a_update_calendars'] = [UpdateH4aCalendarsController::class, 'updateCalendars'];
$GLOBALS['BE_MOD']['content']['calendar']['h4a_update_results'] = [UpdateH4aResultsController::class, 'updateResults'];
$GLOBALS['BE_MOD']['content']['handballnet_teams']['update_hn_teams'] = [UpdateHandballnetTeamsController::class, 'updateTeams'];
$GLOBALS['BE_MOD']['content']['handballnet_teams']['update_hn_clubs'] = [UpdateHandballnetClubSeasonsController::class, 'updateClubSeasons'];

/*
 * tables
 */

$GLOBALS['BE_MOD']['content']['calendar']['tables'] = array_merge(
    $GLOBALS['BE_MOD']['content']['calendar']['tables'],
    ['tl_h4a_seasons', 'tl_hn_teams']
);
$GLOBALS['BE_MOD']['content']['handballnet_teams']['tables'] = ['tl_hn_seasons', 'tl_hn_teams', 'tl_hn_clubs'];

// Register Models
$GLOBALS['TL_MODELS']['tl_hn_seasons'] = HandballnetSeasonsModel::class;
$GLOBALS['TL_MODELS']['tl_hn_teams'] = HandballnetTeamsModel::class;
$GLOBALS['TL_MODELS']['tl_hn_clubs'] = HandballnetClubsModel::class;