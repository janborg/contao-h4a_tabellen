<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
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

        $arrResultSpielplan = Helper::getJsonSpielplan($teamID);

        $ligaID = $arrResultSpielplan['dataList'][0]['gClassID'];

        if (null !== $ligaID) {
            $arrResultTabelle = Helper::getJsonTabelle($ligaID);
        }

        Helper::updateDatabaseFromJsonFile($arrResultSpielplan, $arrResultTabelle);

        $this->io->text('Datenbankeintrag für team_id '.$teamID.' wurde erstellt!');

        return $this->statusCode;
    }

}
