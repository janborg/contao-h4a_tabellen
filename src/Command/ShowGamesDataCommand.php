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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class ShowGamesDataCommand.
 *
 * @property SymfonyStyle $io
 * @property int          $statusCode
 */
#[AsCommand(
    name: 'handballnet:show:gamesdata',
    description: 'Show Summary, Lineups and/or Timeline for a Game from handball.net',
)]
class ShowGamesDataCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        private ContaoFramework $framework,
        private HandballnetApiClient $handballnetApiClient,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('This command allows you to update all Stats for game from handball.net.')
            ->addArgument('gGameID', InputArgument::REQUIRED, 'gGameID from handball.net')
            ->addOption('provider', null, InputOption::VALUE_REQUIRED, 'Provider', '')
            ->addOption('verband', null, InputOption::VALUE_REQUIRED, 'Verband', '')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->framework->initialize();

        $this->io = new SymfonyStyle($input, $output);

        $gGameID = $input->getArgument('gGameID');

        if (!$gGameID) {
            $this->io->error('Bitte Game ID angeben!');

            return Command::FAILURE;
        }

        if ($input->getOption('provider')) {
            $provider = $input->getOption('provider');
        } else {
            $question = new ChoiceQuestion(
                'Bitte wählen Sie den Provider des Vereins:',
                array_column(Provider::cases(), 'value'),
                null,
            );
            $question->setErrorMessage('Bitte gültigen Provider angeben');

            $provider = $this->io->askQuestion($question);
        }

        if ($input->getOption('verband')) {
            $verband = $input->getOption('verband');
        } else {
            $question = new ChoiceQuestion(
                'Bitte wählen Sie den Verband aus, in dem der Verein spielt:',
                array_column(Verband::cases(), 'value'),
                null,
            );

            $question->setErrorMessage('Verband %s ist ungültig.');

            $verband = $this->io->askQuestion($question);
        }

        $id = $provider.'.'.$verband.'.'.$gGameID;

        try {
            $data = json_decode($this->handballnetApiClient->getGameCombinedData($id, false), true);
        } catch (\Exception $e) {
            $this->io->error($e->getMessage());

            return Command::FAILURE;
        }

        $output->writeln([
            '',
            '============================================================',
            '',
            'Wettbewerb: '.$data['data']['summary']['tournament']['name'],
            'Heim: '.$data['data']['summary']['homeTeam']['name'],
            'Gast: '.$data['data']['summary']['awayTeam']['name'],
            'Ergebnis: '.$data['data']['summary']['homeGoals'].':'.$data['data']['summary']['awayGoals'],
            'Zuschauer: '.$data['data']['summary']['attendance'],

            '',
            '============================================================',
            '',
            'Heim Aufstellung: ',
        ]);

        $tablehome = new Table($output);
        $tablehome->setHeaders(['ID', 'Vorname', 'Nachname', 'Position', 'Nr.', 'Tore', '7m-Tore', '7m-Fehlwürfe', '2min', 'Gelb', 'Rot', 'Blau', 'Typ']);
        $tablehome->setRows($data['data']['lineup']['away']);
        $tablehome->render();

        $output->writeln([
            '',
            '============================================================',
            '',
            'Gast Aufstellung: ',
        ]);

        $tableguest = new Table($output);
        $tableguest->setHeaders(['ID', 'Vorname', 'Nachname', 'Position', 'Nr.', 'Tore', '7m-Tore', '7m-Fehlwürfe', '2min', 'Gelb', 'Rot', 'Blau', 'Typ']);
        $tableguest->setRows($data['data']['lineup']['away']);
        $tableguest->render();

        $output->writeln([
            '',
            '============================================================',
            '',
            'Spielverlauf: ',
        ]);

        $tabletimeline = new Table($output);
        $tabletimeline->setHeaders(['ID', 'Type', 'Time', 'Stand', 'Teitstempel', 'Team', 'Text']);
        $tabletimeline->setRows($data['data']['events']);
        $tabletimeline->render();

        return Command::SUCCESS;
    }
}
