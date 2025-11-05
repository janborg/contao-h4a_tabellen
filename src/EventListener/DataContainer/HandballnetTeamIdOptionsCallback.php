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

class HandballnetTeamIdOptionsCallback
{
    /**
     * Callback to get Handballnet Team IDs.
     *
     * @return array<string, string>
     */
    #[AsCallback(table: 'tl_content', target: 'fields.handballnet_team_id.options')]
    public function getHandballnetTeamOptions(DataContainer $dc): array
    {
        $options = [];

    if (!isset($dc->activeRecord->handballnet_saison) || !$dc->activeRecord->handballnet_saison) {
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
            $options[$team->handballnet_team_id] = \sprintf(
                '%s (%s)',
                $team->liga_shortname,
                $team->handballnet_team_id
            );
        }

        return $options;
    }
}
