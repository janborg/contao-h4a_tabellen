<?php

declare(strict_types=1);

namespace Janborg\H4aTabellen\EventListener\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Janborg\H4aTabellen\HandballnetApiClient;

/**
 * Callback to get Club Data from handballnet Api
 */
#[AsCallback(table: 'tl_hn_clubs', target: 'fields.handballnet_id.save')]
class HandballnetCheckClubIdCallback
{
    public function __construct(
        private readonly HandballnetApiClient $handballnetApiClient
    ) {}

    public function __invoke(mixed $value, DataContainer $dc): mixed {
        // Wenn das Feld leer ist, nichts tun
        if (empty($value)) {
            return $value;
        }

        try {
            $clubData = $this->handballnetApiClient->getClubData($value, false);
        } catch (\Exception $e) {
            // Hier kannst du Fehlerbehandlung implementieren 
            // (z.B. eine Meldung im Contao Flash-Messenger anzeigen)
            throw new \Exception('Fehler beim Abrufen der Handballnet Daten: ' . $e->getMessage());
        }

        if (!$clubData) {
            throw new \Exception('Bitte Handballnet Club Id prüfen');
        }

        return $value;
    }
}
