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
use Janborg\H4aTabellen\Model\H4aJsonDataModel;
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

        $arrResultSpielplan = $this->getJsonSpielplan($teamID);

        $ligaID = $arrResultSpielplan['dataList'][0]['gClassID'];

        if ($ligaID != null) {
            $arrResultTabelle = $this->getJsonTabelle($ligaID);
        }
            
        $this->updateDatabaseFromJsonFile($arrResultSpielplan, $arrResultTabelle);         

        $this->io->text('Datenbankeintrag für team_id '.$teamID.' wurde erstellt!');

        return $this->statusCode;
    }

    protected function getJsonSpielplan($teamID)
    {
        $type = 'team';

        $arrResult = null;

        $liga_url = Helper::getURL($type, $teamID);

        $strFilePath = '/files/h4a/team/'.$teamID.'.json';

        $strJson = file_get_contents($liga_url);

        try {
            $arrResult = json_decode($strJson, true);
        } catch (\Exception $e) {
            $this->io->text('Json File für team_id '.$teamID.' konnte nicht erstellt werden!');
        }

        \File::putContent($strFilePath, json_encode($arrResult));
        $this->io->text('Json File wurde in '.$strFilePath.' erstellt!');

        return $arrResult[0];
    }

    protected function getJsonTabelle($ligaID)
    {
        $type = 'liga';

        $arrResult = null;

        $liga_url = Helper::getURL($type,$ligaID);
        
        $strJson = file_get_contents($liga_url);

        try {
            $arrResult = json_decode($strJson, true);
        } catch (\Exception $e) {
            $this->io->text('Json File für liga_id '.$ligaID.' konnte nicht abgerufen werden!');
        }

        return $arrResult[0];
    }



    protected function updateDatabaseFromJsonFile($arrResultSpielplan, $arrResultTabelle)
    {
        $objH4aJsonData = H4aJsonDataModel::findOneBy(
            ['lvTypePathStr=?', 'lvIDPathStr=?'],
            [$arrResultSpielplan['lvTypePathStr'], $arrResultSpielplan['lvIDPathStr']]
        );

        if (null === $objH4aJsonData) {
            $objH4aJsonData = new H4aJsonDataModel();
        } 
        
        $objH4aJsonData->tstamp = time();
        $objH4aJsonData->lvTypePathStr = $arrResultSpielplan['lvTypePathStr'];
        $objH4aJsonData->lvIDPathStr = $arrResultSpielplan['lvIDPathStr'];
        $objH4aJsonData->lvTypeLabelStr = trim($arrResultSpielplan['lvTypeLabelStr'], "/ ");
        $objH4aJsonData->gClassID = $arrResultSpielplan['dataList'][0]['gClassID'];
        $objH4aJsonData->gClassName = $arrResultSpielplan['dataList'][0]['gClassSname'];
        $objH4aJsonData->gTeamJson = json_encode($arrResultSpielplan);
        $objH4aJsonData->gTableJson = json_encode($arrResultTabelle);
        
            
        if(!is_null($arrResultSpielplan['dataList'][0]['gDate'])) {
            $arrDate = explode('.', $arrResultSpielplan['dataList'][0]['gDate']);
            $Unixdate = mktime(0, 0, 0, (int)$arrDate[1], (int)$arrDate[0], (int)$arrDate[2]);
            $objH4aJsonData->DateStart = $Unixdate; 
            $month = date('m', $Unixdate);

            switch (true) {
                case ($month < 4):
                    $objH4aJsonData->season = date('Y', $Unixdate-31536000)."/".(date('Y', $Unixdate));
                    break;
                
                case ($month >= 3):
                    $objH4aJsonData->season = date('Y', $Unixdate)."/".(date('Y', $Unixdate+31536000));
                    break;
            }
        }
        
        if (!is_null($arrResultTabelle['lvTypeLabelStr'])) {
            $objH4aJsonData->gClassNameLong = trim($arrResultTabelle['lvTypeLabelStr'], "/ ");
        }

        $objH4aJsonData->save();

    }
}