<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

namespace Janborg\H4aTabellen\Command;

use Contao\CalendarEventsModel;
use Contao\CalendarModel;
use Contao\CoreBundle\Framework\ContaoFramework;
use Janborg\H4aTabellen\Helper\Helper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class H4aUpdateResultsCommand extends Command
{
    private $io;

    private $statusCode = 0;

    /**
     * @var ContaoFramework
     */
    private $framework;

    public function __construct(ContaoFramework $framework)
    {
        $this->framework = $framework;

        parent::__construct();
    }

    protected function configure(): void
    {
        $commandHelp = 'Update Results in H4a-Events';

        $this->setName('h4a:updateresults')
            ->setDescription($commandHelp)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $this->framework->initialize();

        $this->io = new SymfonyStyle($input, $output);

        $objEvents = CalendarEventsModel::findby(
            ['DATE(FROM_UNIXTIME(startDate)) <= ?', 'h4a_resultComplete != ?', 'gGameNo != ?'],
            [date('Y-m-d'), true, '']
        );

        if (null === $objEvents) {
            $this->io->text('Es wurden keine Events ohne Ergebnis gefunden.');

            return $this->statusCode;
        }

        $this->io->text('Es wurden '.\count($objEvents).' H4a-Events ohne Ergebnis gefunden. Versuche Ergebnisse abzurufen ...');

        foreach ($objEvents as $objEvent) {
            $this->io->text('Versuche Ergebnis für Spiel'.$objEvent->gGameNo.' abzurufen...');

            if ($objEvent->startTime > time() || '00:00' === date('H:i', (int) $objEvent->startTime)) {
                $this->io->text('Spiel ', $objEvent->gGameNo.' ist noch nicht gestartet. Abruch ...');

                continue;
            }

            $objCalendar = CalendarModel::findById($objEvent->pid);

            $arrResult = Helper::getJsonSpielplan($objCalendar->h4a_team_ID);

            $games = $arrResult['dataList'];
            $gameId = array_search($objEvent->gGameNo, array_column($games, 'gNo'), true);

            if (' ' !== $games[$gameId]['gHomeGoals'] && ' ' !== $games[$gameId]['gGuestGoals']) {
                $objEvent->gHomeGoals = $games[$gameId]['gHomeGoals'];
                $objEvent->gGuestGoals = $games[$gameId]['gGuestGoals'];
                $objEvent->gHomeGoals_1 = $games[$gameId]['gHomeGoals_1'];
                $objEvent->gGuestGoals_1 = $games[$gameId]['gGuestGoals_1'];
                $objEvent->h4a_resultComplete = true;
                $objEvent->save();

                $this->io->text('Ergebnis ('.$games[$gameId]['gHomeGoals'].':'.$games[$gameId]['gGuestGoals'].') für Spiel '.$objEvent->gGameNo.' über Handball4all aktualisiert');
            } else {
                $objEvent->h4a_resultComplete = false;

                $this->io->text('Ergebnis für Spiel '.$objEvent->gGameNo.' über Handball4all geprüft, kein Ergebnis vorhanden');
            }
        }

        return $this->statusCode;
    }
}
