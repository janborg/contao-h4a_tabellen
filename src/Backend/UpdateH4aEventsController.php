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
use Contao\CoreBundle\Monolog\SystemLogger;
use Contao\Input;
use Janborg\H4aTabellen\H4aEventAutomator\H4aEventAutomator;

class UpdateH4aEventsController extends Backend
{
    public function __construct(
        private H4aEventAutomator $h4aEventAutomator,
        private SystemLogger|null $systemLogger,
    ) {
        parent::__construct();
        $this->import(BackendUser::class, 'User');
    }

    public function updateEvents(): void
    {
        $id = Input::get('id');

        $objCalendar = CalendarModel::findById($id);

        $this->h4aEventAutomator->syncCalendars($objCalendar);

        $this->systemLogger?->info('Update des Kalenders "'.$objCalendar->title.'" (ID: '.$objCalendar->id.') über Handball4all durchgeführt.');

        $this->redirect($this->getReferer());
    }
}
