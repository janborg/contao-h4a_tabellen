<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

$GLOBALS['BE_MOD']['content']['calendar']['update_events'] = ['Janborg\H4aTabellen\Cron\H4aEventAutomator', 'updateEvents'];
$GLOBALS['BE_MOD']['content']['calendar']['update_calendar'] = ['Janborg\H4aTabellen\Cron\H4aEventAutomator', 'updateArchive'];
$GLOBALS['BE_MOD']['content']['calendar']['update_results'] = ['Janborg\H4aTabellen\Cron\H4aEventAutomator', 'updateResults'];

// Register Cron job
$GLOBALS['TL_CRON']['daily'][] = ['Janborg\H4aTabellen\Cron\H4aEventAutomator', 'updateEvents'];
$GLOBALS['TL_CRON']['hourly'][] = ['Janborg\H4aTabellen\Cron\H4aEventAutomator', 'updateResults'];

// Register Models
$GLOBALS['TL_MODELS']['tl_h4ajsondata'] = Janborg\H4aTabellen\Model\H4aJsonDataModel::class;
