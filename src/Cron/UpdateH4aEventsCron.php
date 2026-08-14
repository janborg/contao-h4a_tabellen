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
use Janborg\H4aTabellen\HandballNet\HandballnetEventAutomator;
use Psr\Log\LoggerInterface;

class UpdateH4aEventsCron
{
    public function __construct(
        private ContaoFramework $framework,
        private HandballnetEventAutomator $handballnetEventAutomator,
        private readonly LoggerInterface|null $logger,
    ) {
        $this->framework->initialize();
    }

    public function updateEvents(): void
    {
        $objCalendars = CalendarModel::findby(
            ['tl_calendar.handballnet_imported=?'],
            [true],
        );

        foreach ($objCalendars as $objCalendar) {
            $this->handballnetEventAutomator->syncCalendars($objCalendar);

            $this->logger?->info('Update des Kalenders "'.$objCalendar->title.'" (ID: '.$objCalendar->id.') über Handball4all durchgeführt.');
        }
    }
}
