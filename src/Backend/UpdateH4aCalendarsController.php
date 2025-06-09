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
use Contao\CalendarModel;
use Contao\Message;
use Janborg\H4aTabellen\H4aEventAutomator\H4aEventAutomator;

class UpdateH4aCalendarsController extends Backend
{
    public function __construct(
        private H4aEventAutomator $h4aEventAutomator,
    ) {
        parent::__construct();
        $this->import(BackendUser::class, 'User');
    }

    public function updateCalendars(): void
    {
        $objCalendars = CalendarModel::findby(
            ['tl_calendar.h4a_imported=?'],
            ['1'],
        );

        if (null === $objCalendars) {
            Message::addInfo('Es wurden keine Kalender zum Update über H4a gefunden.');

            $this->redirect($this->getReferer());
        }

        foreach ($objCalendars as $objCalendar) {
            $this->h4aEventAutomator->syncCalendars($objCalendar, false);
        }

        Message::addConfirmation('Update der Kalender über Handball4all durchgeführt.');

        $this->redirect($this->getReferer());
    }
}
