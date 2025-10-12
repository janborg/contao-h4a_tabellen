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
use Contao\Message;
use Contao\BackendUser;
use Contao\CalendarModel;
use Contao\CalendarEventsModel;
use Contao\CoreBundle\Cache\EntityCacheTags;
use Janborg\H4aTabellen\Helper\H4aApiHelper;
use Janborg\H4aTabellen\Event\H4aResultUpdatedEvent;
use Janborg\H4aTabellen\H4aEventAutomator\H4aEventAutomator;
use Janborg\H4aTabellen\HandballnetApiClient;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class UpdateH4aResultsController extends Backend
{
    public function __construct(
        private EntityCacheTags $entityCacheTags,
        private HandballnetApiClient $handballnetApiClient,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
        parent::__construct();
        $this->import(BackendUser::class, 'User');
    }

    public function updateResults(): void
    {
        $objEvents = CalendarEventsModel::findby(
            ['DATE(FROM_UNIXTIME(startDate)) <= ?', 'h4a_resultComplete != ?', 'gGameID != ?'],
            [date('Y-m-d'), true, ''],
        );

        if (null === $objEvents) {
            Message::addInfo('Es stehen für keine vergangenen Spiele die Ergebnisse aus.');

            $this->redirect($this->getReferer());
        }

        foreach ($objEvents as $objEvent) {
            $now = time();

            // Continue, wenn Spiel noch nicht gestartet
            if ($objEvent->startTime > $now || '00:00' === date('H:i', (int) $objEvent->startTime)) {
                continue;
            }

            $id = $objEvent->provider.'.'.$objEvent->verband.'.'.$objEvent->gGameID;

            try {
                $data = json_decode($this->handballnetApiClient->getGameSummaryData($id, false), true);
            } catch (\Exception $e) {
                $this->io->error($e->getMessage());
            }

            if ( null !== $data['data']['homeGoals'] && null !== $data['data']['awayGoals']) {
                $objEvent->gHomeGoals = $data['data']['homeGoals'];
                $objEvent->gGuestGoals = $data['data']['awayGoals'];
                $objEvent->gHomeGoals_1 = $data['data']['homeGoalsHalf'];
                $objEvent->gGuestGoals_1 = $data['data']['awayGoalsHalf'];
                $objEvent->h4a_resultComplete = true;
                $objEvent->save();

                // Dispatch Event
                $event = new H4aResultUpdatedEvent($objEvent);
                $this->eventDispatcher->dispatch($event);

                // Add message for the updated Event
                Message::addConfirmation('Ergebnis ('.$data['data']['homeGoals'].':'.$data['data']['awayGoals'].' für Spiel '.$objEvent->gGameID.' '.$objEvent->title.' erhalten.');

                // Invalidate CacheTag for Event
                $this->entityCacheTags->invalidateTagsFor($objEvent);
            } else {
                $objEvent->h4a_resultComplete = false;

                Message::addInfo('Ergebnis für Spiel '.$objEvent->gGameID.' '.$objEvent->title.' über Handballnet geprüft, kein Ergebnis vorhanden.');
            }
        }

        $this->redirect($this->getReferer());
    }
}
