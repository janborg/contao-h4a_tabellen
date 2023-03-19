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
use Contao\CalendarModel;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\System;
use Janborg\H4aTabellen\H4aEventAutomator\H4aEventAutomator;
use Psr\Log\LogLevel;

class UpdateH4aCalendarsController extends Backend
{
    public function __construct()
    {
        parent::__construct();
        $this->import('BackendUser', 'User');
    }

    public function updateCalendars(): void
    {
        $objCalendars = CalendarModel::findby(
            ['tl_calendar.h4a_imported=?', 'tl_calendar.h4a_ignore !=?'],
            ['1', '1']
        );

        if (null === $objCalendars) {
            System::getContainer()
                ->get('monolog.logger.contao')
                ->log(LogLevel::INFO, 'Es wurden keine Kalender zum Update über H4a gefunden.', ['contao' => new ContaoContext(__CLASS__.'::'.__FUNCTION__, TL_GENERAL)])
            ;
            $this->redirect($this->getReferer());
        }

        foreach ($objCalendars as $objCalendar) {
            $h4aeventautomator = new H4aEventAutomator();

            $h4aeventautomator->syncCalendars($objCalendar);
        }

        System::getContainer()
            ->get('monolog.logger.contao')
            ->log(LogLevel::INFO, 'Update der Kalender über Handball4all durchgeführt.', ['contao' => new ContaoContext(__CLASS__.'::'.__FUNCTION__, TL_GENERAL)])
        ;

        $this->redirect($this->getReferer());
    }
}
