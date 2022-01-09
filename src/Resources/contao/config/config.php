<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

use Janborg\H4aTabellen\Model\H4aJsonDataModel;
use Janborg\H4aTabellen\Model\H4aSeasonModel;

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

$GLOBALS['BE_MOD']['content']['calendar']['update_events'] = ['Janborg\H4aTabellen\H4aEventAutomator\H4aEventAutomator', 'updateEvents'];
$GLOBALS['BE_MOD']['content']['calendar']['update_calendar'] = ['Janborg\H4aTabellen\H4aEventAutomator\H4aEventAutomator', 'updateArchive'];
$GLOBALS['BE_MOD']['content']['calendar']['update_results'] = ['Janborg\H4aTabellen\H4aEventAutomator\H4aEventAutomator', 'updateResults'];

/*
 * tables
 */

$GLOBALS['BE_MOD']['content']['calendar']['tables'][] = 'tl_h4a_seasons';

// Register Models
$GLOBALS['TL_MODELS']['tl_h4ajsondata'] = H4aJsonDataModel::class;
$GLOBALS['TL_MODELS']['tl_h4a_seasons'] = H4aSeasonModel::class;
