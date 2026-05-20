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
use Janborg\H4aTabellen\Model\HandballnetSeasonsModel;

class HandballnetSeasonOptionsCallback
{
    /**
     * Callback to get Handballnet Tournament IDs.
     *
     * @return array<int, string>
     */
    #[AsCallback(table: 'tl_calendar', target: 'fields.h4a_saison.options')]
    public function getHandballnetSeasonOptions(DataContainer $dc): array
    {
        $options = [];

        $seaons = HandballnetSeasonsModel::findBy(
            ['is_active=?'],
            [true],
            ['order' => 'season_name DESC'],
        );

        if (null === $seaons) {
            return $options;
        }

        foreach ($seaons as $season) {
            $options[$season->id] = \sprintf(
                '%s - %s',
                $season->season_name,
                $season->club_name,
            );
        }

        return $options;
    }
}
