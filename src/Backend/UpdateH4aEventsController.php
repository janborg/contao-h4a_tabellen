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
use Contao\Message;
use Janborg\H4aTabellen\HandballNet\HandballnetEventAutomator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UpdateH4aEventsController extends Backend
{
    public function __construct(
        private HandballnetEventAutomator $handballnetEventAutomator,
        private UrlGeneratorInterface $urlGenerator,
    ) {
        parent::__construct();
        $this->import(BackendUser::class, 'User');
    }

    public function updateEvents(): void
    {
        $id = Input::get('id');

        $objCalendar = CalendarModel::findById($id);

        $this->handballnetEventAutomator->syncCalendars($objCalendar);

        Message::addConfirmation('Update des Kalenders "'.$objCalendar->title.'" (ID: '.$objCalendar->id.') über Handballnet durchgeführt.');

        $this->redirect($this->urlGenerator->generate('contao_backend', ['do' => 'calendar', 'table' => 'tl_calendar_events', 'id' => $id]));
    }
}
