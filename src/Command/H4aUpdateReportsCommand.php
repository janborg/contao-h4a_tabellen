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
use Symfony\Component\Console\Style\SymfonyStyle;

class H4aUpdateReportsCommand extends Command
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
        $commandHelp = 'Update ReportNo in H4a-Events';

        $this->setName('h4a:updatereports')
            ->setDescription($commandHelp)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $this->framework->initialize();

        $this->io = new SymfonyStyle($input, $output);

        $objEvents = CalendarEventsModel::findby(
            ['DATE(FROM_UNIXTIME(startDate)) <= ?', 'h4a_resultComplete = ?', 'sGID = ?'],
            [date('Y-m-d'), true, '']
        );

        if (null === $objEvents) {
            $this->io->text('Es wurden keine Events mit Ergebnis, aber ohne ReportNo (sGID) gefunden.');

            return $this->statusCode;
        }

        $this->io->text('Es wurden '.\count($objEvents).' H4a-Events mit Ergebnis, aber ohne ReportNo (sGID) gefunden. Versuche ReportNo abzurufen ...');

        foreach ($objEvents as $objEvent) {
            $this->io->text('Versuche ReportNo für Spiel '.$objEvent->title.' ('.$objEvent->gGameNo.') abzurufen...');

            $sGID = Helper::getReportNo($objEvent->gClassID, $objEvent->gGameNo);

            if (isset($sGID) && null !== $sGID) {
                $objEvent->sGID = $sGID;
                $objEvent->save();

                $this->io->text('ReportNo (sGID) '.$sGID.' für Spiel  '.$objEvent->title.' ('.$objEvent->gGameNo.') über Handball4all erhalten.');
            }
            else {
                $this->io->text('ReportNo (sGID) für Spiel  '.$objEvent->title.' ('.$objEvent->gGameNo.') konnte nicht ermittelt werden.');
            }
        }

        return $this->statusCode;
    }
}
