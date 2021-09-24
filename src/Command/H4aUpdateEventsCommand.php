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

use Contao\CoreBundle\Framework\ContaoFramework;
use Janborg\H4aTabellen\Helper\Helper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Contao\CalendarEventsModel;
use Contao\CalendarModel;
use Janborg\H4aTabellen\H4aEventAutomator\H4aEventAutomator;

class H4aUpdateEventsCommand extends Command
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
        $commandHelp = 'Update H4a-Events';

        $this->setName('h4a:updateevents')
            ->setDescription($commandHelp)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $this->framework->initialize();

        $this->io = new SymfonyStyle($input, $output);

        $objCalendars = CalendarModel::findby(
            ['tl_calendar.h4a_imported=?', 'tl_calendar.h4a_ignore !=?'],
            ['1', '1']
        );
        
        if (null === $objCalendars) {

            $this->io->text('Es wurden keine Kalender zum Update über H4a gefunden.');

            return $this->statusCode;
        }
        else {
            $this->io->text('Es wurden '.\count($objCalendars). ' Kalender zum Update über H4a gefunden gefunden.');
        }

        foreach ($objCalendars as $objCalendar) {

            $this->io->text('Versuche Events für Kalender'.$objCalendar->title.' (Id: '.$objCalendar->id.') abzurufen...');

            $h4aeventautomator = new H4aEventAutomator;
            
            $h4aeventautomator->syncCalendars($objCalendar);

            $this->io->text('Update des Kalenders "'.$objCalendar->title.'" (ID: '.$objCalendar->id.') über Handball4all durchgeführt.');
            
        }

        return $this->statusCode;
    }
}