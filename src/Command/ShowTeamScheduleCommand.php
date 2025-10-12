<?php

declare(strict_types=1);

/*
 * This file is part of contao-handballnet.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

namespace Janborg\H4aTabellen\Command;

use Symfony\Component\Console\Helper\Table;
use Janborg\H4aTabellen\HandballNet\Verband;
use Janborg\H4aTabellen\HandballNet\Provider;
use Janborg\H4aTabellen\HandballnetApiClient;
use Symfony\Component\Console\Command\Command;
use Contao\CoreBundle\Framework\ContaoFramework;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

/**
 * Class ShowTeamSceduleCommand.
 *
 * @property SymfonyStyle $io
 * @property int          $statusCode
 */
#[AsCommand(
    name: 'handballnet:show:schedule',
    description: 'Show schedule of a given team from handballnet',
)]
class ShowTeamScheduleCommand extends Command
{
    public function __construct(
        private ContaoFramework $framework,
        private HandballnetApiClient $handballnetApiClient,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('This command allows you to show all Games of a team from handball.net.')
            ->addOption('teamID', null, InputOption::VALUE_REQUIRED, 'teamID from handball.net')
            ->addOption('provider', null, InputOption::VALUE_REQUIRED, 'handball4all, nuliga oder sportradar')
            ->addOption('verband', null, InputOption::VALUE_REQUIRED, 'verband from handball.net, z.B. baden')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->framework->initialize();

        $this->io = new SymfonyStyle($input, $output);

        // clubID
        $teamID = $input->getOption('teamID');

        if (!$teamID) {
            $this->io->error('Bitte die team ID (--teamID) angeben');

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

        $id = $provider.'.'.$verband.'.'.$teamID;

        try {
            $data = json_decode($this->handballnetApiClient->getTeamScheduleData($id, false), true);
            } catch (\Exception $e) {
            $this->io->error($e->getMessage());

            return Command::FAILURE;
        }

        $this->io->info('Games for TeamID: '.$teamID.' (Verband: '.$verband.', Provider: '.$provider.')');

        $tableGames = new Table($output);
        $tableGames->setHeaders(['ID', 'HomeTeam', 'GuestTeam', 'homeGoals', 'guestGoals', 'attendance']);

        $games = [];
        foreach ($data['data'] as $game) {
            $games[] = [
                'id' => $game['id'],
                'homeTeam' => $game['homeTeam']['name'],
                'guestTeam' => $game['awayTeam']['name'],
                'homeGoals' => $game['homeGoals'],
                'guestGoals' => $game['awayGoals'],
                'attendance' => $game['attendance']
            ];
        }
        $tableGames->setRows($games);
        $tableGames->render();

        return Command::SUCCESS;
    }
}
