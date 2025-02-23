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
use Janborg\H4aTabellen\Crawler\VerbandsCrawler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UpdateLineupCommand.
 *
 * @property SymfonyStyle $io
 * @property int          $statusCode
 */
class ShowVerbaendeCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'h4a:show:verbaende';

    /**
     * @var string
     */
    protected static $defaultDescription = 'Show all verbaende from handballnet';

    public function __construct(
        private ContaoFramework $framework,
        private VerbandsCrawler $verbandsCrawler,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('This command allows you to show all Verbaende from handball.net.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->framework->initialize();

        $verbaende = $this->verbandsCrawler->getAllVerbaende();

        $table = new Table($output);
        $table->setHeaders(['Url', 'Name', 'ShortName']);
        $table->setRows($verbaende);
        $table->render();

        return Command::SUCCESS;
    }
}
