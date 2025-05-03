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
use Contao\CoreBundle\Monolog\SystemLogger;
use Contao\CoreBundle\Framework\ContaoFramework;
use Janborg\H4aTabellen\Crawler\H4aReportNoCrawler;
use Janborg\H4aTabellen\Event\H4aReportUpdatedEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class UpdateH4aReportNoCron
{
    public function __construct(
        private ContaoFramework $framework,
        private SystemLogger $systemLogger,
        private H4aReportNoCrawler $h4aReportNoCrawler,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
        $this->framework->initialize();
    }

    public function updateReportNo(): void
    {
        $objEvents = CalendarEventsModel::findby(
            ['DATE(FROM_UNIXTIME(startDate)) <= ?', 'h4a_resultComplete = ?', 'sGID = ?'],
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
            if (null === $objEvent->provider || null === $objEvent->verband || null === $objEvent->gClassName) {
                continue;
            }
            $this->h4aReportNoCrawler->setProvider($objEvent->provider);
            $this->h4aReportNoCrawler->setClassID($objEvent->gClassID);
            $this->h4aReportNoCrawler->setClassShortName($objEvent->gClassName);
            $this->h4aReportNoCrawler->setgGameID($objEvent->gGameID);
            $this->h4aReportNoCrawler->setVerbandName($objEvent->verband);
            $this->h4aReportNoCrawler->crawlReportNo();

            $sGID = $this->h4aReportNoCrawler->getSGid();

            if ('' !== $sGID) {
                $objEvent->sGID = $sGID;
                $objEvent->save();

                // Dispatch Event
                $event = new H4aReportUpdatedEvent($objEvent);
                $this->eventDispatcher->dispatch($event);
                
                $this->systemLogger
                    ->info('Report Nr. '.$objEvent->sGID.' für Spiel '.$objEvent->title.' ('.$objEvent->gGameID.') über Handball4all gespeichert')
                ;
            } else {
                $this->systemLogger
                    ->info('Report Nr. für Spiel '.$objEvent->title.' ('.$objEvent->gGameID.') konnte nicht ermittelt werden')
                ;
            }
        }
    }
}
