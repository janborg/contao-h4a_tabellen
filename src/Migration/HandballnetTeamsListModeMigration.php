<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

namespace Janborg\H4aTabellen\Migration;

use Contao\CalendarModel;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;
use Janborg\H4aTabellen\HandballNet\HandballNetTeam;
use Janborg\H4aTabellen\Helper\H4aApiHelper;
use Janborg\H4aTabellen\Model\H4aSeasonModel;
use Janborg\H4aTabellen\Model\HandballnetTeamsModel;

class HandballnetTeamsListModeMigration extends AbstractMigration
{
    public function __construct(
        private Connection $connection,
        private ContaoFramework $framework,
    ) {
    }

    public function shouldRun(): bool
    {
        $schemaManager = $this->connection->createSchemaManager();

        $columns = $schemaManager->listTableColumns('tl_hn_teams');

        // If the field pid does not exist in tl_hn_teams we should do nothing
        if (!isset($columns['pid'])) {
            return false;
        }

        // find all tl_hn_teams with empty pid
        $teamsWithoutPid = $this->connection->executeQuery('
                SELECT * FROM tl_hn_teams WHERE pid = NULL
            ')
            ->fetchAllAssociative(); 

        return
            (!empty($teamsWithoutPid)) ? true : false;
    }

    public function run(): MigrationResult
    {
        $this->framework->initialize();

        // foreach tl_h4a_season, find all tl_hn_teams where hn_season equals saison
        $seasons = H4aSeasonModel::findAll();

        foreach ($seasons as $season) {
            $hn_teams = HandballnetTeamsModel::findBy(
                'saison', $season->hn_season
            );

            foreach ($hn_teams as $hn_team) {
                $hn_team->pid = $season->id;
                $hn_team->save();
            }
        }

        return $this->createResult(
            true,
            'Added pid to all teams without a parent season.',
        );
    }
}
