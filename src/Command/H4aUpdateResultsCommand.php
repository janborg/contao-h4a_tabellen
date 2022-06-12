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


class H4aUpdateResultsCommand extends Command
{
    protected static $defaultName = 'h4a:update:results';

    protected static $defaultDescription = 'Update results for all H4a-Events';


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
        $this->setHelp('With this command you can update the results for all H4a Events');
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $this->framework->initialize();

        $output->writeln(
            'Suche alle H4a-Events von heute oder früher ohne Ergebnis...'
        );

        $objEvents = CalendarEventsModel::findby(
            ['DATE(FROM_UNIXTIME(startDate)) <= ?', 'h4a_resultComplete != ?', 'gGameNo != ?'],
            [date('Y-m-d'), true, '']
        );

        if (null === $objEvents) {
            $output->writeln([
                'Es wurden keine Events ohne Ergebnis gefunden.',
                '',
                'Ende',
                ''
            ]);

            return Command::SUCCESS;
        }

        $output->writeln([
            'Es wurden ' . \count($objEvents) . ' H4a-Events ohne Ergebnis gefunden. ',
            '',
            '===============================================================',
            ''
        ]);

        foreach ($objEvents as $objEvent) {
            $output->writeln([
                'Spiel ' . $objEvent->gGameNo . ' ' . $objEvent->title . ':',
                '-----------------------------------------------------',
                'Versuche Ergebnis abzurufen...'
            ]);

            $now = time();

            if ($objEvent->startTime > $now || '00:00' === date('H:i', (int) $objEvent->startTime)) {
                $output->writeln([
                    'Spiel ist noch nicht gestartet. Abruch ...',
                    ''
                ]);

                continue;
            }

            $objCalendar = CalendarModel::findById($objEvent->pid);
            
            $h4a_team_ID = Helper::getH4ateamFromH4aSeasons($objCalendar, $objEvent);
            
            $arrResult = Helper::getJsonSpielplan($h4a_team_ID);

            if (!isset($arrResult['dataList'][0])) {
                $output->writeln([
                    'Spielplan für Team' . $objCalendar->h4a_team_ID . ' konnte nicht abgerufen werden.',
                    'Abruch ...',
                    ''
                ]);

                continue;
            }

            $games = $arrResult['dataList'];

            $gameId = array_search($objEvent->gGameNo, array_column($games, 'gNo'), true);

            if (' ' !== $games[$gameId]['gHomeGoals'] && ' ' !== $games[$gameId]['gGuestGoals']) {
                $objEvent->gHomeGoals = $games[$gameId]['gHomeGoals'];
                $objEvent->gGuestGoals = $games[$gameId]['gGuestGoals'];
                $objEvent->gHomeGoals_1 = $games[$gameId]['gHomeGoals_1'];
                $objEvent->gGuestGoals_1 = $games[$gameId]['gGuestGoals_1'];
                $objEvent->h4a_resultComplete = true;
                $objEvent->save();

                $output->writeln([
                    'Ergebnis (' . $games[$gameId]['gHomeGoals'] . ':' . $games[$gameId]['gGuestGoals'] . ' erhalten',
                    ''
                ]);
            } else {
                $objEvent->h4a_resultComplete = false;

                $output->writeln([
                    'Ergebnis über Handball4all geprüft, kein Ergebnis vorhanden',
                    ''
                ]);
            }
        }

        return Command::SUCCESS;
    }
}
