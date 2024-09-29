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
use Contao\CoreBundle\Cache\EntityCacheTags;
use Contao\CoreBundle\Framework\ContaoFramework;
use Janborg\H4aTabellen\Helper\Helper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class H4aUpdateResultsCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'h4a:update:results';

    /**
     * @var string
     */
    protected static $defaultDescription = 'Update results for all H4a-Events';

    public function __construct(
        private ContaoFramework $framework,
        private EntityCacheTags $entityCacheTags,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('With this command you can update the results for all H4a Events');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->framework->initialize();

        $output->writeln(
            'Suche alle H4a-Events von heute oder früher ohne Ergebnis...',
        );

        $objEvents = CalendarEventsModel::findby(
            ['DATE(FROM_UNIXTIME(startDate)) <= ?', 'h4a_resultComplete != ?', 'gGameID != ?'],
            [date('Y-m-d'), true, ''],
        );

        if (null === $objEvents) {
            $output->writeln([
                'Es wurden keine Events ohne Ergebnis gefunden.',
                '',
                'Ende',
                '',
            ]);

            return Command::SUCCESS;
        }

        $output->writeln([
            'Es wurden '.\count($objEvents).' H4a-Events ohne Ergebnis gefunden. ',
            '',
            '===============================================================',
            '',
        ]);

        foreach ($objEvents as $objEvent) {
            $output->writeln([
                'Spiel '.$objEvent->gGameID.' '.$objEvent->title.':',
                '-----------------------------------------------------',
                'Versuche Ergebnis abzurufen...',
            ]);

            $now = time();

            if ($objEvent->startTime > $now || '00:00' === date('H:i', (int) $objEvent->startTime)) {
                $output->writeln([
                    '<comment>Spiel ist noch nicht gestartet. Abruch ...</comment>',
                    '',
                ]);

                continue;
            }

            $objCalendar = CalendarModel::findById($objEvent->pid);

            $h4a_team_ID = Helper::getH4ateamFromH4aSeasons($objCalendar, $objEvent);

            $arrResult = Helper::getJsonSpielplan($h4a_team_ID);

            if (!isset($arrResult['dataList'][0])) {
                $output->writeln([
                    '<error>Spielplan für Team'.$objCalendar->h4a_team_ID.' konnte nicht abgerufen werden.</error>',
                    'Abruch ...',
                    '',
                ]);

                continue;
            }

            $games = $arrResult['dataList'];

            $gameId = array_search($objEvent->gGameID, array_column($games, 'gID'), true);

            if (' ' !== $games[$gameId]['gHomeGoals'] && ' ' !== $games[$gameId]['gGuestGoals']) {
                $objEvent->gHomeGoals = $games[$gameId]['gHomeGoals'];
                $objEvent->gGuestGoals = $games[$gameId]['gGuestGoals'];
                $objEvent->gHomeGoals_1 = $games[$gameId]['gHomeGoals_1'];
                $objEvent->gGuestGoals_1 = $games[$gameId]['gGuestGoals_1'];
                $objEvent->h4a_resultComplete = true;
                $objEvent->save();

                $output->writeln([
                    '<info>Ergebnis ('.$games[$gameId]['gHomeGoals'].':'.$games[$gameId]['gGuestGoals'].' erhalten</info>',
                    '',
                ]);

                // Invalidate CacheTag for Event
                $this->entityCacheTags->invalidateTagsFor($objEvent);
            } else {
                $objEvent->h4a_resultComplete = false;

                $output->writeln([
                    '<comment>Ergebnis über Handball4all geprüft, kein Ergebnis vorhanden</comment>',
                    '',
                ]);
            }
        }

        return Command::SUCCESS;
    }
}
