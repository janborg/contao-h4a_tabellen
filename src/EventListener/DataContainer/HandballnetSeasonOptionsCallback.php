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
    #[AsCallback(table: 'tl_calendar', target: 'fields.handballnet_season.options')]
    #[AsCallback(table: 'tl_content', target: 'fields.handballnet_season.options')]
    public function getHandballnetSeasonOptions(DataContainer $dc): array
    {
        $options = [];

        if (isset($dc->activeRecord->handballnet_club)) {
            $arrCol = ['pid=?', 'is_active=?'];
            $arrVal = [$dc->activeRecord->handballnet_club, true];
        } else {
            $arrCol = ['is_active=?'];
            $arrVal = [true];
        }

        $seasons = HandballnetSeasonsModel::findBy(
            $arrCol,
            $arrVal,
            ['order' => 'season_name DESC'],
        );

        if (null === $seasons) {
            return $options;
        }

        foreach ($seasons as $season) {
            $options[$season->id] = \sprintf(
                '%s - %s',
                $season->season_name,
                $season->club_name,
            );
        }

        return $options;
    }
}
