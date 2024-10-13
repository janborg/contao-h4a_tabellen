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

use Contao\CalendarModel;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Monolog\SystemLogger;
use Janborg\H4aTabellen\H4aEventAutomator\H4aEventAutomator;

class UpdateH4aEventsCron
{
    public function __construct(
        private ContaoFramework $framework,
        private H4aEventAutomator $h4aEventAutomator,
        private SystemLogger $systemLogger,
    ) {
        $this->framework->initialize();
    }

    public function updateEvents(): void
    {
        $objCalendars = CalendarModel::findby(
            ['tl_calendar.h4a_imported=?'],
            ['1'],
        );

        foreach ($objCalendars as $objCalendar) {
            $this->h4aEventAutomator->syncCalendars($objCalendar);

            $this->systemLogger
                ->info('Update des Kalenders "'.$objCalendar->title.'" (ID: '.$objCalendar->id.') über Handball4all durchgeführt.')
            ;
        }
    }
}
