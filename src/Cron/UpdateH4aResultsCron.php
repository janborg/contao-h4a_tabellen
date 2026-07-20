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

use Contao\CalendarEventsModel;
use Contao\CoreBundle\Cache\EntityCacheTags;
use Contao\CoreBundle\Framework\ContaoFramework;
use Janborg\H4aTabellen\Event\H4aResultUpdatedEvent;
use Janborg\H4aTabellen\HandballnetApiClient;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class UpdateH4aResultsCron
{
    public function __construct(
        private ContaoFramework $framework,
        private EntityCacheTags $entityCacheTags,
        private readonly LoggerInterface|null $logger,
        private HandballnetApiClient $handballnetApiClient,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
        $this->framework->initialize();
    }

    public function updateResults(): void
    {
        $objEvents = CalendarEventsModel::findby(
            ['DATE(FROM_UNIXTIME(startDate)) <= ?', 'h4a_resultComplete != ?', 'handballnet_id != ?'],
            [date('Y-m-d'), true, ''],
            [
                'eager' => true,
                'having' => 'h4a_season__is_active = 1',
            ],
        );

        if (null === $objEvents) {
            return;
        }

        foreach ($objEvents as $objEvent) {
            $now = time();

            // Continue, wenn Spiel noch nicht gestartet
            if ($objEvent->startTime > $now || '00:00' === date('H:i', (int) $objEvent->startTime)) {
                continue;
            }

            try {
                $data = json_decode($this->handballnetApiClient->getGameSummaryData($objEvent->handballnet_id), true);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
                continue;
            }

            if ('Post' === $data['data']['state']) {
                $objEvent->gHomeGoals = $data['data']['homeGoals'];
                $objEvent->gGuestGoals = $data['data']['awayGoals'];
                $objEvent->gHomeGoals_1 = $data['data']['homeGoalsHalf'];
                $objEvent->gGuestGoals_1 = $data['data']['awayGoalsHalf'];
                $objEvent->h4a_resultComplete = true;
                $objEvent->save();

                // Dispatch Event
                $event = new H4aResultUpdatedEvent($objEvent);
                $this->eventDispatcher->dispatch($event);

                // log new result
                $this->logger
                    ->info('Ergebnis ('.$data['data']['homeGoals'].':'.$data['data']['awayGoals'].') für Spiel '.$objEvent->gGameID.' über Handballnet aktualisiert')
                ;

                // Invalidate CacheTag for Event
                $this->entityCacheTags->invalidateTagsFor($objEvent);
            } else {
                $objEvent->h4a_resultComplete = false;

                $this->logger
                    ->info('Ergebnis für Spiel '.$objEvent->title.' ('.$objEvent->gGameID.') über Handball4all geprüft, kein Ergebnis vorhanden')
                ;
            }
        }
    }
}
