<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

use Janborg\H4aTabellen\Model\H4aSeasonModel;
use Janborg\H4aTabellen\Model\H4aJsonDataModel;
use Janborg\H4aTabellen\Model\HandballnetTeamsModel;
use Janborg\H4aTabellen\Model\HandballnetSeasonsModel;
use Janborg\H4aTabellen\Backend\UpdateH4aEventsController;
use Janborg\H4aTabellen\Backend\UpdateH4aResultsController;
use Janborg\H4aTabellen\Backend\UpdateH4aCalendarsController;
use Janborg\H4aTabellen\Backend\UpdateHandballnetTeamsController;

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

/*
 * tables
 */

$GLOBALS['BE_MOD']['content']['calendar']['tables'] = array_merge(
    $GLOBALS['BE_MOD']['content']['calendar']['tables'],
    ['tl_h4a_seasons', 'tl_hn_teams']
);

// Register Models
$GLOBALS['TL_MODELS']['tl_h4ajsondata'] = H4aJsonDataModel::class;
$GLOBALS['TL_MODELS']['tl_h4a_seasons'] = H4aSeasonModel::class;
$GLOBALS['TL_MODELS']['tl_hn_teams'] = HandballnetTeamsModel::class;