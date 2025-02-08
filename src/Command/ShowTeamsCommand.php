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
use Janborg\H4aTabellen\Crawler\TeamsCrawler;
use Symfony\Component\Console\Command\Command;
use Contao\CoreBundle\Framework\ContaoFramework;
use Janborg\H4aTabellen\Crawler\VerbandsCrawler;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

/**
 * Class UpdateLineupCommand.
 *
 * @property SymfonyStyle $io
 * @property int          $statusCode
 */
class ShowTeamsCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'h4a:show:teams';

    /**
     * @var string
     */
    protected static $defaultDescription = 'Show teams of a given club from handballnet';

    public function __construct(
        private ContaoFramework $framework,
        private TeamsCrawler $teamsCrawler,
        private VerbandsCrawler $verbandsCrawler,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('This command allows you to show all Teams for a club from handball.net.')
            ->addOption('clubID', null, InputOption::VALUE_REQUIRED , 'clubID from handball.net')
            ->addOption('provider', null, InputOption::VALUE_REQUIRED, 'handball4all, nuliga oder sportradar')
            ->addOption('verband', null, InputOption::VALUE_REQUIRED , 'verband from handball.net, z.B. baden')
            ->addOption('season', null, InputOption::VALUE_REQUIRED , 'season from handball.net, z.B. 2024')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->framework->initialize();

        $io = new SymfonyStyle($input, $output);

        // clubID
        $clubID = $input->getOption('clubID');

        if (!$clubID) {
            $io->error('Bitte die Club ID (--clubID) angeben');
            return Command::FAILURE;
        }

        $this->teamsCrawler->setClubID($clubID);

        //provider
        $provider = $input->getOption('provider');

        if(!$provider) {
            $question = new ChoiceQuestion(
                'Bitte wählen Sie den Provider des Vereins:',
                ['handball4all', 'nuliga', 'sportradar'],
                null
            );

            $question->setErrorMessage('Bitte gültigen Provider angeben');

            $provider = $io->askQuestion($question);
        }

        $this->teamsCrawler->setProvider($provider);

        //verband
        $verband = $input->getOption('verband');

        if (!$verband) {    
            $verbaende = $this->verbandsCrawler->getAllVerbaende();

            $verbaendeShorts = array_map(function ($verband) {
                return $verband['verbandShortName'];
            }, $verbaende);

            $question = new ChoiceQuestion(
                'Bitte wählen Sie den Verband aus, in dem der verein spielt:',
                $verbaendeShorts,
                null
            );

            $question->setErrorMessage('Verband %s ist ungültig.');

            $verband = $io->askQuestion($question);
        }

        $this->teamsCrawler->setVerbandName($verband);        

        // season
        $season = $input->getOption('season');

        if ($season) {
            $this->teamsCrawler->setSeason($season);
        }

        try {
            $teams = $this->teamsCrawler->getAllTeams();
        } catch (\Exception $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }
        
        $io->info('Teams for ClubID: '.$clubID.' (Verband: '.$verband.'in der Saison: '.$season.')');

        $tablehome = new Table($output);
        $tablehome->setHeaders(['TeamUrl', 'Team', 'TeamID', 'Provider', 'Verband']);
        $tablehome->setRows($teams);
        $tablehome->render();

        return Command::SUCCESS;
    }
}
