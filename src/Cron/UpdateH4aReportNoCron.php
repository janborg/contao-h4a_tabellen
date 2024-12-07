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
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Monolog\SystemLogger;
use Janborg\H4aTabellen\Helper\H4aApiHelper;

class UpdateH4aReportNoCron
{
    public function __construct(
        private ContaoFramework $framework,
        private SystemLogger $systemLogger,
        private H4aApiHelper $h4aApiHelper,
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
                'having' => 'h4a_season__h4a_ignore = 0',
            ],
        );

        if (null === $objEvents) {
            return;
        }

        foreach ($objEvents as $objEvent) {
            $sGID = $this->h4aApiHelper->getReportNo($objEvent->gClassID, $objEvent->gGameNo);

            if (isset($sGID) && null !== $sGID) {
                $objEvent->sGID = $sGID;
                $objEvent->save();

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
