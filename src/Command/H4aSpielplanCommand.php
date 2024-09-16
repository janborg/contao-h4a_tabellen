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
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class H4aSpielplanCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'h4a:show:spielplan';

    /**
     * @var string
     */
    protected static $defaultDescription = 'Show H4a Spielplan for given teamID';

    public function __construct(private ContaoFramework $framework)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $commandHelp = 'Zeigt den Spielplan und die Tabelle einer Mannschaft und speichert die json dazu in Contao ab';
        $parameterLigaIDHelp = 'Id des Teams, für das die Daten angezeigt werden sollen';

        $this->setHelp($commandHelp)
            ->addArgument('teamID', InputArgument::REQUIRED, $parameterLigaIDHelp)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->framework->initialize();

        $teamID = $input->getArgument('teamID');

        $arrResultSpielplan = Helper::getJsonSpielplan($teamID);

        $table = new Table($output);
        $table->setHeaders(['Datum', 'Uhrzeit', 'Heim', 'Gast', 'Ergebnis']);

        if (!isset($arrResultSpielplan['dataList'][0])) {
            $output->writeln('<error>Keine Daten für Spielplan gefunden</error>');

            return \Symfony\Component\Console\Command\Command::SUCCESS;
        }

        $spielplan = [];

        foreach ($arrResultSpielplan['dataList'] as $spiel) {
            $spielplan[$spiel['gNo']] = [
                'datum' => $spiel['gDate'],
                'uhrzeit' => $spiel['gTime'],
                'heim' => $spiel['gHomeTeam'],
                'gast' => $spiel['gGuestTeam'],
                'ergebnis' => $spiel['gHomeGoals'].':'.$spiel['gGuestGoals'],
            ];
        }

        $table->setRows($spielplan);
        $table->render();

        $ligaID = $arrResultSpielplan['dataList'][0]['gClassID'] ?? null;

        if (null !== $ligaID) {
            $arrResultTabelle = Helper::getJsonTabelle($ligaID);
        } else {
            $output->writeln('<error>Keine Daten für Tabelle gefunden</error>');

            return Command::SUCCESS;
        }

        $table = new Table($output);
        $table->setHeaders(['Platz', 'Team', 'Spiele', 'Punkte', 'Tore']);

        if (!isset($arrResultTabelle['dataList'][0])) {
            $output->writeln('<error>Keine Daten für Spielplan gefunden</error>');

            return Command::SUCCESS;
        }

        $liga = [];

        foreach ($arrResultTabelle['dataList'] as $team) {
            $liga[$team['tabScore']] = [
                'platz' => $team['tabScore'],
                'team' => $team['tabTeamname'],
                'spiele' => $team['numPlayedGames'],
                'punkte' => $team['pointsPlus'].' : '.$team['pointsMinus'],
                'tore' => $team['numGoalsShot'].' : '.$team['numGoalsGot'],
            ];
        }

        $table->setRows($liga);
        $table->render();

        Helper::updateDatabaseFromJsonFile($arrResultSpielplan, $arrResultTabelle);

        $output->writeln('<info>Datenbankeinträge für team_id '.$teamID.' wurde erstellt!</info>');

        return Command::SUCCESS;
    }
}
