<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_commands.
 * (c) Jan Lünborg
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

class H4aSpielplanCommand extends Command
{
    private $io;

    private $statusCode = 0;

    /**
     * @var ContaoFramework
     */
    private $framework;

    /**
     * @var string
     */
    private $projectDir;

    public function __construct(ContaoFramework $framework, string $projectDir)
    {
        $this->framework = $framework;
        $this->projectDir = $projectDir;

        parent::__construct();
    }

    protected function configure(): void
    {
        $commandHelp = 'Ruft die json Datei für den Spielplan einer Mannschaft ab';
        $parameterLigIDHelp = 'Id des Teams, für das die json Datei abgerufen werden soll';
        $argument = new InputArgument('teamID', InputArgument::REQUIRED, $parameterLigIDHelp);

        $this->setName('h4a:spielplan')
             ->setDefinition([$argument])
             ->setDescription($commandHelp);
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $this->framework->initialize();

        $this->io = new SymfonyStyle($input, $output);

        $teamID = $input->getArgument('teamID');

        $this->getJsonTabelle($teamID);

        return $this->statusCode;
    }

    protected function getJsonTabelle($teamID): void
    {
        $type = 'team';

        $arrResult = null;

        $liga_url = Helper::getURL($type, $teamID);

        $strFilePath = '/files/h4a/team'.$teamID.'.json';

        $strJson = file_get_contents($liga_url);

        try {
            $arrResult = json_decode($strJson, true);
        } catch (\Exception $e) {
            $this->io->text('Json File für team_id '.$teamID.' konnte nicht erstellt werden!');
        }

        \File::putContent($strFilePath, json_encode($arrResult));
        $this->io->text('Json File erstellt wurde in '.$strFilePath.' erstellt!');
    }
}