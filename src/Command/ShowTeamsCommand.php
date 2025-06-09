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
use Janborg\H4aTabellen\Crawler\TeamsCrawler;
use Janborg\H4aTabellen\HandballNet\HandballNetTeam;
use Janborg\H4aTabellen\HandballNet\Provider;
use Janborg\H4aTabellen\HandballNet\Verband;
use Janborg\H4aTabellen\Model\H4aSeasonModel;
use Janborg\H4aTabellen\Model\HandballnetTeamsModel;
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
    private SymfonyStyle $io;

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

        // save teams (optional)
        if ($input->getOption('save-teams')) {
            $this->saveTeams($teams, $season);
        }

        $this->io->info('Teams for ClubID: '.$clubID.' (Verband: '.$verband.' in der Saison: '.$season.')');

        $tableTeams = new Table($output);
        $tableTeams->setHeaders(['Team', 'team_id', 'liga_name', 'liga_id', 'liga_short_name', 'provider', 'verband']);

        // teamUrl nicht ausgeben
        foreach ($teams as &$team) {
            $team = (array) $team;
            unset($team['team_url']);
            $team['provider'] = $team['provider']->toString();
            $team['verband'] = $team['verband']->toString();
        }

        $tableTeams->setRows($teams);
        $tableTeams->render();

        return Command::SUCCESS;
    }

    /**
     * Save teams to database into tl_hn_teams.
     *
     * @param array<int, HandballNetTeam> $teams
     */
    protected function saveTeams(array $teams, string $season): void
    {
        $h4aSeason = H4aSeasonModel::findBy(
            'hn_season',
            $season,
        );
        
        foreach ($teams as $team) {
            $handballnetTeam = HandballnetTeamsModel::findBy(
                ['team_id=?', 'liga_id=?', 'liga_shortname=?'],
                [$team->team_id, $team->liga_id, $team->liga_short_name],
            );

            // skip already existing teams
            if ($handballnetTeam) {
                $this->io->info('Team '.$team->team_id.' ('.$team->liga_id.', '.$team->liga_short_name.') already exists in Database');
                continue;
            }

            // skip teams without team_id or liga_id
            if (empty($team->team_id) || empty($team->liga_id)) {
                $this->io->error('Team '.$team->team_name.' ('.$team->liga_short_name.') has no team_id or liga_id');
                continue;
            }

            // create new teams
            $handballnetTeamsModel = new HandballnetTeamsModel();

            $handballnetTeamsModel->pid = $h4aSeason->id;
            $handballnetTeamsModel->saison = $season;
            $handballnetTeamsModel->team_id = $team->team_id;
            $handballnetTeamsModel->liga_id = $team->liga_id;
            $handballnetTeamsModel->provider = $team->provider->toString();
            $handballnetTeamsModel->verband = $team->verband->toString();
            $handballnetTeamsModel->liga_shortname = $team->liga_short_name;
            $handballnetTeamsModel->liga_name = $team->liga_name;
            $handballnetTeamsModel->my_team_name = $team->team_name;
            $handballnetTeamsModel->is_active = true;
            $handballnetTeamsModel->tstamp = time();

            $handballnetTeamsModel->save();

            $this->io->info('Team '.$handballnetTeamsModel->team_id.' ('.$handballnetTeamsModel->liga_id.', '.$handballnetTeamsModel->liga_shortname.') saved to Database');
        }
    }
}
