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

use Symfony\Component\Console\Helper\Table;
use Janborg\H4aTabellen\HandballNet\Verband;
use Janborg\H4aTabellen\Crawler\TeamsCrawler;
use Janborg\H4aTabellen\HandballNet\Provider;
use Symfony\Component\Console\Command\Command;
use Contao\CoreBundle\Framework\ContaoFramework;
use Janborg\H4aTabellen\Model\HandballnetModel;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Janborg\H4aTabellen\Model\HandballnetTeamsModel;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

/**
 * Class UpdateLineupCommand.
 *
 * @property SymfonyStyle $io
 * @property int          $statusCode
 */
#[AsCommand(
    name: 'h4a:show:teams',
    description: 'Show teams of a given club from handballnet',
)]
class ShowTeamsCommand extends Command
{
    public function __construct(
        private ContaoFramework $framework,
        private TeamsCrawler $teamsCrawler,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('This command allows you to show all Teams for a club from handball.net.')
            ->addOption('clubID', null, InputOption::VALUE_REQUIRED, 'clubID from handball.net')
            ->addOption('provider', null, InputOption::VALUE_REQUIRED, 'handball4all, nuliga oder sportradar')
            ->addOption('verband', null, InputOption::VALUE_REQUIRED, 'verband from handball.net, z.B. baden')
            ->addOption('season', null, InputOption::VALUE_REQUIRED, 'season from handball.net, z.B. 2024')
            ->addOption('save-teams', null, InputOption::VALUE_NONE, 'Save Teams to Database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->framework->initialize();

        $this->io = new SymfonyStyle($input, $output);

        // clubID
        $clubID = $input->getOption('clubID');

        if (!$clubID) {
            $this->io->error('Bitte die Club ID (--clubID) angeben');

            return Command::FAILURE;
        }

        $this->teamsCrawler->setClubID($clubID);

        // provider
        $provider = $input->getOption('provider');

        if (!$provider) {
            $question = new ChoiceQuestion(
                'Bitte wählen Sie den Provider des Vereins:',
                array_column(Provider::cases(), 'value'),
                null,
            );

            $question->setErrorMessage('Bitte gültigen Provider angeben');

            $provider = $this->io->askQuestion($question);
        }

        $this->teamsCrawler->setProvider($provider);

        // verband
        $verband = $input->getOption('verband');

        if (!$verband) {
            $question = new ChoiceQuestion(
                'Bitte wählen Sie den Verband aus, in dem der verein spielt:',
                array_column(Verband::cases(), 'value'),
                null,
            );

            $question->setErrorMessage('Verband %s ist ungültig.');

            $verband = $this->io->askQuestion($question);
        }

        $this->teamsCrawler->setVerbandName($verband);

        // season
        $season = $input->getOption('season');

        if (!$season) {
            $question = new Question('Bitte geben Sie die Saison im Format "YYYY" an', '2024');
            
            $season = $this->io->askQuestion($question);   
        }    
        
        $this->teamsCrawler->setSeason($season);
         
        try {
            $teams = $this->teamsCrawler->getAllTeams();
        } catch (\Exception $e) {
            $this->io->error($e->getMessage());

            return Command::FAILURE;
        }

        $this->io->info('Teams for ClubID: '.$clubID.' (Verband: '.$verband.' in der Saison: '.$season.')');

        $tablehome = new Table($output);
        $tablehome->setHeaders(['Team', 'className', 'TeamID', 'Provider', 'Verband', 'classID', 'classShortName']);

        // teamUrl nicht ausgeben
        foreach ($teams as &$team) {
            unset($team['teamUrl']);
        }

        $tablehome->setRows($teams);
        $tablehome->render();

        if ($input->getOption('save-teams')) {
            $this->saveTeams($teams, $season);
        }

        return Command::SUCCESS;
    }

    // save temas to database into tl_handballnet_teams
    protected function saveTeams(array $teams, string $season): void
    {
        foreach ($teams as $team) {

            $handballnetTeam = HandballnetTeamsModel::findBy(
                ['team_id=?', 'liga_id=?', 'liga_shortname=?'], 
                [$team['teamID'], $team['classID'], $team['classShortName']]
            );

            if ($handballnetTeam) {
                $this->io->info('Team '.$team['teamID'].' ('.$team['classID'].', '.$team['classShortName'].') already exists in Database');
                continue;
            }

            $handballnetTeamsModel = new HandballnetTeamsModel();

            $handballnetTeamsModel->saison = $season ?? null;
            $handballnetTeamsModel->team_id = $team['teamID'] ?? null;
            $handballnetTeamsModel->liga_id = $team['classID'] ?? null;
            $handballnetTeamsModel->provider = $team['provider'] ?? null;
            $handballnetTeamsModel->verband = $team['verband'] ?? null;
            $handballnetTeamsModel->liga_shortname = $team['classShortName'] ?? null;
            $handballnetTeamsModel->liga_name = isset($team['ligaName']) ? trim(str_replace($team['teamName'], '', $team['ligaName'])) : null;
            $handballnetTeamsModel->my_team_name = $team['teamName'] ?? null;

            $handballnetTeamsModel->save();
        
            $this->io->info('Team '.$team['teamID'].' ('.$team['classID'].', '.$team['classShortName'].') saved to Database');
        }
    }
}