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

use Contao\CalendarEventsModel;
use Contao\CoreBundle\Cache\EntityCacheTags;
use Contao\CoreBundle\Framework\ContaoFramework;
use Janborg\H4aTabellen\Event\H4aResultUpdatedEvent;
use Janborg\H4aTabellen\HandballNet\Enum\GameState;
use Janborg\H4aTabellen\HandballnetApiClient;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class H4aUpdateResultsCommand.
 *
 * @property SymfonyStyle $io
 * @property int          $statusCode
 */
#[AsCommand(
    name: 'handballnet:update:results',
    description: 'Update results for all H4a-Events',
)]
class H4aUpdateResultsCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        private ContaoFramework $framework,
        private EntityCacheTags $entityCacheTags,
        private HandballnetApiClient $handballnetApiClient,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('With this command you can update the results for all H4a Events');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->framework->initialize();

        $this->io = new SymfonyStyle($input, $output);

        $this->io->info('Suche alle H4a-Events von heute oder früher ohne Ergebnis...');

        $objEvents = CalendarEventsModel::findBy(
            ['DATE(FROM_UNIXTIME(startDate)) <= ?', 'hn_resultComplete != ?', 'handballnet_game_id != ?'],
            [date('Y-m-d'), true, ''],
        );

        if (null === $objEvents) {
            $output->writeln([
                'Es wurden keine Events ohne Ergebnis gefunden.',
                '',
                'Ende',
                '',
            ]);

            return Command::SUCCESS;
        }

        $output->writeln([
            'Es wurden '.\count($objEvents).' H4a-Events ohne Ergebnis gefunden. ',
            '',
            '===============================================================',
            '',
        ]);

        foreach ($objEvents as $objEvent) {
            $output->writeln([
                'Spiel '.$objEvent->handballnet_game_id.' '.$objEvent->title.':',
                '-----------------------------------------------------',
                'Versuche Ergebnis abzurufen...',
            ]);

            $now = time();

            if ($objEvent->startTime > $now || '00:00' === date('H:i', (int) $objEvent->startTime)) {
                $output->writeln([
                    '<comment>Spiel ist noch nicht gestartet. Abbruch ...</comment>',
                    '',
                ]);

                continue;
            }

            try {
                $data = json_decode($this->handballnetApiClient->getGameSummaryData($objEvent->handballnet_game_id, false), true);
            } catch (\Exception $e) {
                $this->io->error($e->getMessage());
                continue;
            }

            $state = $data['data']['state'] ?? null;

            try {
                $objEvent->handballnet_state = GameState::from($state)->value;
            } catch (\ValueError $e) {
                $this->io->warning('Unbekannter handballnet_state "'.$state.'" für Spiel '.$objEvent->handballnet_game_id);
            }

            if (GameState::POST->value === $state) {
                $objEvent->homeGoals = $data['data']['homeGoals'];
                $objEvent->awayGoals = $data['data']['awayGoals'];
                $objEvent->homeGoalsHalf = $data['data']['homeGoalsHalf'] ?? '';
                $objEvent->awayGoalsHalf = $data['data']['awayGoalsHalf'] ?? '';
                $objEvent->hn_resultComplete = true;
                $objEvent->save();

                $output->writeln([
                    '<info>Ergebnis ('.$data['data']['homeGoals'].':'.$data['data']['awayGoals'].') erhalten</info>',
                    '',
                ]);

                // Dispatch Event
                $event = new H4aResultUpdatedEvent($objEvent);
                $this->eventDispatcher->dispatch($event);

                // Invalidate CacheTag for Event
                $this->entityCacheTags->invalidateTagsFor($objEvent);
            } else {
                $objEvent->hn_resultComplete = false;
                $objEvent->save();

                $output->writeln([
                    '<comment>Ergebnis über handball.net geprüft, kein Ergebnis vorhanden</comment>',
                    '',
                ]);
            }
        }

        return Command::SUCCESS;
    }
}
