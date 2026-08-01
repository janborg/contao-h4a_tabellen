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
        $this->setHelp('This command allows you to update all events that are linked to handballnet');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->framework->initialize();

        $output->writeln('Suche Kalender mit H4a-Events:');

        $objCalendars = CalendarModel::findBy(
            ['tl_calendar.handballnet_imported=?'],
            [true],
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
            'Es wurden '.\count($objCalendars).' Kalender zum Update über Handballnet gefunden.',
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

            try {
                $this->h4aEventAutomator->syncCalendars($objCalendar, false);

                $output->writeln([
                    '<info>Update des Kalenders über Handballnet durchgeführt.</info>',
                    '',
                ]);
            } catch (\Throwable $e) {
                $output->writeln([
                    '<error>Update des Kalenders fehlgeschlagen: '.$e->getMessage().'</error>',
                    '',
                ]);
            }
        }

        return Command::SUCCESS;
    }
}
