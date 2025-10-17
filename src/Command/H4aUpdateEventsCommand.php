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

use Contao\CalendarModel;
use Contao\CoreBundle\Framework\ContaoFramework;
use Janborg\H4aTabellen\H4aEventAutomator\H4aEventAutomator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @property SymfonyStyle $io
 * @property int          $statusCode
 */
#[AsCommand(
    name: 'handballnet:update:events',
    description: 'Update all Events from h4a',
)]
class H4aUpdateEventsCommand extends Command
{
    public function __construct(
        private ContaoFramework $framework,
        private H4aEventAutomator $h4aEventAutomator,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('This command allows youto update all events that are linked to handballnet');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->framework->initialize();

        $output->writeln('Suche Kalender mit H4a-Events:');

        $objCalendars = CalendarModel::findby(
            ['tl_calendar.h4a_imported=?'],
            ['1'],
        );

        if (null === $objCalendars) {
            $output->writeln([
                '<comment>Es wurden keine Kalender zum Update über Handballnet gefunden.</comment>',
                '',
                'Ende!',
                '',
            ]);

            return Command::SUCCESS;
        }

        $output->writeln([
            'Es wurden '.\count($objCalendars).' Kalender zum Update über Handballnet gefunden gefunden.',
            'Versuche nun die Updates der Kalender durchzuführen',
            '==========================================================',
            '',
        ]);

        foreach ($objCalendars as $objCalendar) {
            $output->writeln([
                '',
                'Kalender: '.$objCalendar->title,
                '-----------------------------------------------------',
                '',
            ]);
            $output->writeln('Starte Update...');

            $this->h4aEventAutomator->syncCalendars($objCalendar, false);

            $output->writeln([
                '<info>Update des Kalenders über Handballnet durchgeführt.</info>',
                '',
            ]);
        }

        return Command::SUCCESS;
    }
}
