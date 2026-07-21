<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

namespace Janborg\H4aTabellen\EventListener\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Contao\Message;
use Janborg\H4aTabellen\HandballnetApiClient;

/**
 * Callback to get Club Data from handballnet Api.
 */
#[AsCallback(table: 'tl_hn_clubs', target: 'fields.handballnet_id.save')]
class HandballnetCheckClubIdCallback
{
    public function __construct(private readonly HandballnetApiClient $handballnetApiClient)
    {
    }

    public function __invoke(mixed $value, DataContainer $dc): mixed
    {
        // Wenn das Feld leer ist, nichts tun
        if (empty($value)) {
            return $value;
        }

        try {
            $clubData = $this->handballnetApiClient->getClubData($value, false);
        } catch (\Exception $e) {
            Message::addError('Fehler beim Abruf der Daten von handball.net ('.$e->getMessage().')');

            throw new \Exception('Fehler beim Abrufen der Handballnet Daten: '.$e->getMessage());
        }

        if (!$clubData) {
            throw new \Exception('Bitte Handballnet Club Id prüfen');
        }

        return $value;
    }
}
