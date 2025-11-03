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
use Janborg\H4aTabellen\Model\HandballnetTeamsModel;

class HandballnetTournamentIdOptionsCallback
{
    /**
     * Callback to get Handballnet Tournament IDs.
     *
     * @return array<string, string>
     */
    #[AsCallback(table: 'tl_content', target: 'fields.handballnet_tournament_id.options')]
    public function getHandballnetTournamentIdOptions(DataContainer $dc): array
    {
        $options = [];

        if (!isset($dc->activeRecord) || !isset($dc->activeRecord->handballnet_saison)) {
            return $options;
        }

        $teams = HandballnetTeamsModel::findBy(
            ['pid=?'],
            [$dc->activeRecord->handballnet_saison],
            ['order' => 'liga_shortname ASC'],
        );

        if (null === $teams) {
            return $options;
        }

        foreach ($teams as $team) {
            $options[$team->handballnet_tournament_id] = \sprintf(
                '%s (%s)',
                $team->liga_shortname,
                $team->my_team_name,
            );
        }

        return $options;
    }
}
