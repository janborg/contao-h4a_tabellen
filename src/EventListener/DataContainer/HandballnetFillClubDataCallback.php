<?php

declare(strict_types=1);

namespace Janborg\H4aTabellen\EventListener\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Janborg\H4aTabellen\HandballnetApiClient;

/**
 * Callback to get Club Data from handballnet Api
 */
#[AsCallback(table: 'tl_hn_clubs', target: 'config.onbeforesubmit')]
class HandballnetFillClubDataCallback
{
    public function __construct(
        private readonly HandballnetApiClient $handballnetApiClient
    ) {}

    public function __invoke(array $record, DataContainer $dc): array {
        // Wenn das Feld leer ist, nichts tun
        if (empty($record['handballnet_id'])) {
            return $record;
        }

        try {
            $clubData = $this->handballnetApiClient->getClubData((string) $record['handballnet_id'], false);
        } catch (\Exception $e) {
            // Hier kannst du Fehlerbehandlung implementieren 
            // (z.B. eine Meldung im Contao Flash-Messenger anzeigen)
            throw new \Exception('Fehler beim Abrufen der Handballnet Daten: ' . $e->getMessage());
        }

        if (!$clubData) {
            return $record;
        }

        $clubData = json_decode($clubData, true)['data'];

        $record['name'] = $clubData['name'];
        $record['acronym'] = $clubData['acronym'];
        $record['org_id'] = $clubData['organization']['id'];
        $record['org_name'] = $clubData['organization']['name'];
        $record['org_acronym'] = $clubData['organization']['acronym'];
        
        return $record;
    }
}
