<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

namespace Janborg\H4aTabellen\Event;

use Contao\CalendarEventsModel;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * This event is dispatched each time a new report (sGID) is upddated from handballnet.
 */
final class H4aReportUpdatedEvent extends Event
{
    public function __construct(
        public readonly CalendarEventsModel $calendarEvent,
    ) {
    }
}
