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
use Contao\CoreBundle\Framework\ContaoFramework;
use Janborg\H4aTabellen\Helper\Helper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
            $sGID = Helper::getReportNo($objEvent->gClassID, $objEvent->gGameNo);

            if (isset($sGID) && null !== $sGID) {
                $objEvent->sGID = $sGID;
                $objEvent->save();

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
