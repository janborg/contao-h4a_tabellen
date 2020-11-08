<?php declare(strict_types = 1);

namespace Janborg\H4aTabellen\Command;

use Symfony\Component\Console\Command\Command;
use Contao\CoreBundle\Framework\ContaoFramework;
use Janborg\H4aTabellen\Helper\Helper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Janborg\H4aTabellen\Model\H4aJsonDataModel;

class H4aTabelleCommand extends Command
{
    private $io;

    private $statusCode =0;

    /**
     * @var ContaoFramework
     */
    private $framework;

    public function __construct(ContaoFramework $framework,$projectDir)
    {
        $this->framework = $framework;
        $this->projectDir = $projectDir;

        parent::__construct();
    }

    protected function configure(): void
    {
        $commandHelp            = 'Ruft die json Datei für eine Tabelle ab';
        $parameterLigIDHelp     = 'Id der Liga, für die die json Datei abgerufen werden soll';
        $argument               = new InputArgument('ligaID', InputArgument::REQUIRED, $parameterLigIDHelp);

        $this->setName('h4a:tabelle')
             ->setDefinition([$argument])
             ->setDescription($commandHelp);
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $this->framework->initialize();

        $this->io = new SymfonyStyle($input, $output);

        $ligaID =$input->getArgument('ligaID');

        $arrResultTabelle = $this->getJsonTabelle($ligaID);

        $this->updateDatabaseFromJsonFile($arrResultTabelle);

        return $this->statusCode;
    }

    protected function getJsonTabelle($ligaID)
    {
        $type = 'liga';

        $arrResult = null;

        $liga_url = Helper::getURL($type,$ligaID);
        
        $strFilePath = '/files/h4a/liga/'.$ligaID.'.json';

        $strJson = file_get_contents($liga_url);

        try {
            $arrResult = json_decode($strJson, true);
        } catch (\Exception $e) {
            $this->io->text('Json File für liga_id '.$ligaID.' konnte nicht erstellt werden!');
        }

        \File::putContent($strFilePath, json_encode($arrResult));
        $this->io->text('Json File erstellt wurde in '.$strFilePath.' erstellt!');

        return $arrResult[0];

    }

    protected function updateDatabaseFromJsonFile($arrResult)
    {
        $objH4aJsonData = H4aJsonDataModel::findOneBy(
            ['lvTypePathStr=?', 'lvIDPathStr=?'],
            [$arrResult['lvTypePathStr'], $arrResult['lvIDPathStr']]
        );

        if (null === $objH4aJsonData) {
            $objH4aJsonData = new H4aJsonDataModel();
        }   

        $objH4aJsonData->tstamp = time();
        $objH4aJsonData->lvTypePathStr = $arrResult['lvTypePathStr'];
        $objH4aJsonData->lvIDPathStr = $arrResult['lvIDPathStr'];
        $objH4aJsonData->lvTypeLabelStr = trim($arrResult['lvTypeLabelStr'], "/ ");
        
        $objH4aJsonData->save();
    }
}