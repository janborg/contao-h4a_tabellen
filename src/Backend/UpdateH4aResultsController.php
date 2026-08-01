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
use Contao\CalendarEventsModel;
use Contao\CoreBundle\Cache\EntityCacheTags;
use Contao\Message;
use Janborg\H4aTabellen\Event\H4aResultUpdatedEvent;
use Janborg\H4aTabellen\HandballnetApiClient;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class UpdateH4aResultsController extends Backend
{
    public function __construct(
        private EntityCacheTags $entityCacheTags,
        private HandballnetApiClient $handballnetApiClient,
        private readonly EventDispatcherInterface $eventDispatcher,
        private UrlGeneratorInterface $urlGenerator,
    ) {
        parent::__construct();
        $this->import(BackendUser::class, 'User');
    }

    public function updateResults(): void
    {
        $objEvents = CalendarEventsModel::findby(
            ['DATE(FROM_UNIXTIME(startDate)) <= ?', 'hn_resultComplete != ?', 'handballnet_game_id != ?'],
            [date('Y-m-d'), true, ''],
        );

        if (null === $objEvents) {
            Message::addInfo('Es stehen für keine vergangenen Spiele die Ergebnisse aus.');

            $this->redirect($this->urlGenerator->generate('contao_backend', ['do' => 'calendar']));
        }

        foreach ($objEvents as $objEvent) {
            $now = time();

            // Continue, wenn Spiel noch nicht gestartet
            if ($objEvent->startTime > $now || '00:00' === date('H:i', (int) $objEvent->startTime)) {
                continue;
            }

            try {
                $data = json_decode($this->handballnetApiClient->getGameSummaryData($objEvent->handballnet_game_id), true);
            } catch (\Exception $e) {
                Message::addError($e->getMessage());
                continue;
            }

            if ('Post' === $data['data']['state']) {
                $objEvent->homeGoals = $data['data']['homeGoals'];
                $objEvent->awayGoals = $data['data']['awayGoals'];
                $objEvent->homeGoalsHalf = $data['data']['homeGoalsHalf'];
                $objEvent->awayGoalsHalf = $data['data']['awayGoalsHalf'];
                $objEvent->hn_resultComplete = true;
                $objEvent->save();

                // Dispatch Event
                $event = new H4aResultUpdatedEvent($objEvent);
                $this->eventDispatcher->dispatch($event);

                // Add message for the updated Event
                Message::addConfirmation('Ergebnis '.$data['data']['homeGoals'].':'.$data['data']['awayGoals'].' für Spiel '.$objEvent->handballnet_game_id.' '.$objEvent->title.' erhalten.');

                // Invalidate CacheTag for Event
                $this->entityCacheTags->invalidateTagsFor($objEvent);
            } else {
                $objEvent->h4a_resultComplete = false;

                Message::addInfo('Ergebnis für Spiel '.$objEvent->handballnet_game_id.' '.$objEvent->title.' über Handballnet geprüft, kein Ergebnis vorhanden.');
            }
        }

        $this->redirect($this->urlGenerator->generate('contao_backend', ['do' => 'calendar']));
    }
}
