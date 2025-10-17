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
use Janborg\H4aTabellen\HandballNet\Provider;
use Janborg\H4aTabellen\HandballNet\Verband;
use Janborg\H4aTabellen\HandballnetApiClient;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class ShowClubTeamsCommand.
 *
 * @property SymfonyStyle $io
 * @property int          $statusCode
 */
#[AsCommand(
    name: 'handballnet:show:teams',
    description: 'Show teams of a given club from handballnet',
)]
class ShowClubTeamsCommand extends Command
{
    public function __construct(
        private ContaoFramework $framework,
        private HandballnetApiClient $handballnetApiClient,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('This command allows you to show all Teams for a club from handball.net.')
            ->addOption('clubID', null, InputOption::VALUE_REQUIRED, 'clubID from handball.net')
            ->addOption('provider', null, InputOption::VALUE_REQUIRED, 'handball4all, nuliga oder sportradar')
            ->addOption('verband', null, InputOption::VALUE_REQUIRED, 'verband from handball.net, z.B. baden')
            ->addOption('season', null, InputOption::VALUE_REQUIRED, 'season from handball.net, z.B. 2025')
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

        // season
        $season = $input->getOption('season');

        if (!$season) {
            $question = new Question('Bitte geben Sie die Saison im Format "YYYY" an', '2025');

            $season = $this->io->askQuestion($question);
        }

        $id = $provider.'.'.$verband.'.'.$clubID;

        try {
            $data = json_decode($this->handballnetApiClient->getClubTeamsData($id, $season, false), true);
        } catch (\Exception $e) {
            $this->io->error($e->getMessage());

            return Command::FAILURE;
        }

        $this->io->info('Teams for ClubID: '.$clubID.' (Verband: '.$verband.', Provider: '.$provider.')');

        $tablehome = new Table($output);
        $tablehome->setHeaders(['ID', 'TeamName', 'classID', 'className', 'classShortName']);

        $teams = [];

        foreach ($data['data'] as $team) {
            $teams[] = [
                'id' => $team['id'],
                'name' => $team['name'],
                'classID' => $team['defaultTournament']['id'],
                'className' => $team['defaultTournament']['name'],
                'classAcronym' => $team['defaultTournament']['acronym'],
            ];
        }
        $tablehome->setRows($teams);
        $tablehome->render();

        return Command::SUCCESS;
    }
}
