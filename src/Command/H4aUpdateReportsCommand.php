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
use Symfony\Component\Console\Command\Command;
use Contao\CoreBundle\Framework\ContaoFramework;
use Symfony\Component\Console\Attribute\AsCommand;
use Janborg\H4aTabellen\Crawler\H4aReportNoCrawler;
use Symfony\Component\Console\Input\InputInterface;
use Janborg\H4aTabellen\Event\H4aReportUpdatedEvent;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsCommand(
    name: 'h4a:update:reports',
    description: 'Update ReportNo in all Events from h4a',
)]
class H4aUpdateReportsCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'h4a:update:reports';

    /**
     * @var string
     */
    protected static $defaultDescription = 'Update ReportNo in all Events from h4a';

    public function __construct(
        private ContaoFramework $framework,
        private H4aReportNoCrawler $h4aReportNoCrawler,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('With this command you can update the ReportNo for all H4a Events');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->framework->initialize();

        $output->writeln(
            'Suche alle H4a-Events mitErgebnis und ohne ReportNo:',
        );

        $objEvents = CalendarEventsModel::findby(
            ['DATE(FROM_UNIXTIME(startDate)) <= ?', 'h4a_resultComplete = ?', 'sGID = ?'],
            [date('Y-m-d'), true, ''],
        );

        if (null === $objEvents) {
            $output->writeln([
                'Es wurden keine Events mit Ergebnis, aber ohne ReportNo (sGID) gefunden.',
                '',
                'Ende',
                '',
            ]);

            return Command::SUCCESS;
        }

        $output->writeln([
            'Es wurden '.\count($objEvents).' H4a-Events mit Ergebnis, aber ohne ReportNo (sGID) gefunden.',
            'Versuche nun die ReportNo abzurufen ...',
            '==============================================================',
            '',
        ]);

        foreach ($objEvents as $objEvent) {
            $output->writeln([
                '',
                'Spiel '.$objEvent->gGameID.' '.$objEvent->title.':',
                '-----------------------------------------------------',
            ]);

            if (null === $objEvent->provider || null === $objEvent->verband || null === $objEvent->gClassName) {
                $output->writeln([
                    '<error>Provider, Verband und/oder LigaShortName ist nicht gesetzt.</error>',
                    '',
                ]);

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
                
                $output->writeln([
                    '<info>ReportNo (sGID) '.$sGID.' über Handball4all erhalten.</info>',
                    '',
                ]);
            } else {
                $output->writeln([
                    '<error>ReportNo (sGID) konnte nicht ermittelt werden.</error>',
                    '',
                ]);
            }
        }

        return Command::SUCCESS;
    }
}
