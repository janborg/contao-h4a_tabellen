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
use Contao\Input;
use Contao\System;
use Janborg\H4aTabellen\H4aEventAutomator\H4aEventAutomator;

class UpdateH4aEventsController extends Backend
{
    public function __construct()
    {
        parent::__construct();
        $this->import(BackendUser::class, 'User');
    }

    public function updateEvents(): void
    {
        $id = [Input::get('id')];

        $objCalendar = CalendarModel::findById($id);

        $h4aeventautomator = new H4aEventAutomator();

        $h4aeventautomator->syncCalendars($objCalendar);

        System::getContainer()
            ->get('monolog.logger.contao.general')
            ->info('Update des Kalenders "'.$objCalendar->title.'" (ID: '.$objCalendar->id.') über Handball4all durchgeführt.')
        ;

        $this->redirect($this->getReferer());
    }
}
