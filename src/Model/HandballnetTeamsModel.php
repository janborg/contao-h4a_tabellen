<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

namespace Janborg\H4aTabellen\Model;

use Contao\Model;
use Contao\Model\Collection;
use Janborg\H4aTabellen\HandballNet\Enum\AgeGroup;
use Janborg\H4aTabellen\HandballNet\Enum\Provider;
use Janborg\H4aTabellen\HandballNet\Enum\Verband;

/**
 * Reads and writes handballnetTeams.
 *
 * @property int    $id
 * @property int    $pid
 * @property int    $tstamp
 * @property string $saison
 * @property string $team_logo
 * @property string $team_group_id
 * @property string $liga_shortname
 * @property string $liga_name
 * @property string $provider
 * @property string $verband
 * @property string $handballnet_team_id
 * @property string $handballnet_tournament_id
 * @property string $tournament_type
 * @property string $age_group
 * @property string $my_team_name
 * @property bool   $is_active
 *
 * @method static HandballnetTeamsModel|null             findById($id, array<string, mixed> $opt=array())
 * @method static HandballnetTeamsModel|null             findByPk($id, array<string, mixed> $opt=array())
 * @method static HandballnetTeamsModel|null             findByIdOrAlias($val, array<string, mixed> $opt=array())
 * @method static HandballnetTeamsModel|null             findOneBy($col, $val, array<string, mixed> $opt=array())
 * @method static HandballnetTeamsModel|null             findOneByPid($val, array<string, mixed> $opt=array())
 * @method static HandballnetTeamsModel|null             findOneBySaison($val, array<string, mixed> $opt=array())
 * @method static HandballnetTeamsModel|null             findOneByTeam_logo($val, array<string, mixed> $opt=array())
 * @method static HandballnetTeamsModel|null             findOneByTeam_group_id($val, array<string, mixed> $opt=array())
 * @method static HandballnetTeamsModel|null             findOneByLiga_shortname($val, array<string, mixed> $opt=array())
 * @method static HandballnetTeamsModel|null             findOneByLiga_name($val, array<string, mixed> $opt=array())
 * @method static HandballnetTeamsModel|null             findOneByProvider($val, array<string, mixed> $opt=array())
 * @method static HandballnetTeamsModel|null             findOneByVerband($val, array<string, mixed> $opt=array())
 * @method static HandballnetTeamsModel|null             findOneByHandballnet_team_id($val, array<string, mixed> $opt=array())
 * @method static HandballnetTeamsModel|null             findOneByHandballnet_tournament_id($val, array<string, mixed> $opt=array())
 * @method static HandballnetTeamsModel|null             findOneByTournament_type($val, array<string, mixed> $opt=array())
 * @method static HandballnetTeamsModel|null             findOneByAge_group($val, array<string, mixed> $opt=array())
 * @method static HandballnetTeamsModel|null             findOneByMy_team_name($val, array<string, mixed> $opt=array())
 * @method static HandballnetTeamsModel|null             findOneByIs_active($val, array<string, mixed> $opt=array())
 * @method static Collection<HandballnetTeamsModel>|null findMultipleByIds($ids, array<string, mixed> $opt=array())
 * @method static Collection<HandballnetTeamsModel>|null findBy($col, $val, array<string, mixed> $opt=array())
 * @method static Collection<HandballnetTeamsModel>|null findByPid($val, array<string, mixed> $opt=array())
 * @method static Collection<HandballnetTeamsModel>|null findBySaison($val, array<string, mixed> $opt=array())
 * @method static Collection<HandballnetTeamsModel>|null findByTeam_logo($val, array<string, mixed> $opt=array())
 * @method static Collection<HandballnetTeamsModel>|null findByTeam_group_id($val, array<string, mixed> $opt=array())
 * @method static Collection<HandballnetTeamsModel>|null findByLiga_shortname($val, array<string, mixed> $opt=array())
 * @method static Collection<HandballnetTeamsModel>|null findByLiga_name($val, array<string, mixed> $opt=array())
 * @method static Collection<HandballnetTeamsModel>|null findByProvider($val, array<string, mixed> $opt=array())
 * @method static Collection<HandballnetTeamsModel>|null findByVerband($val, array<string, mixed> $opt=array())
 * @method static Collection<HandballnetTeamsModel>|null findByHandballnet_team_id($val, array<string, mixed> $opt=array())
 * @method static Collection<HandballnetTeamsModel>|null findByHandballnet_tournament_id($val, array<string, mixed> $opt=array())
 * @method static Collection<HandballnetTeamsModel>|null findByTournament_type($val, array<string, mixed> $opt=array())
 * @method static Collection<HandballnetTeamsModel>|null findByAge_group($val, array<string, mixed> $opt=array())
 * @method static Collection<HandballnetTeamsModel>|null findByMy_team_name($val, array<string, mixed> $opt=array())
 * @method static Collection<HandballnetTeamsModel>|null findByIs_active($val, array<string, mixed> $opt=array())
 * @method static Collection<HandballnetTeamsModel>|null findAll(array<string, mixed> $opt=array())
 * @method static Collection<HandballnetTeamsModel>|null findMultipleByIdsAndSaison($ids, $saison, array<string, mixed> $opt=array())
 * @method static int                                    countById($id, array<string, mixed> $opt=array())
 * @method static int                                    countByPid($val, array<string, mixed> $opt=array())
 * @method static int                                    countBySaison($val, array<string, mixed> $opt=array())
 * @method static int                                    countByTeam_logo($val, array<string, mixed> $opt=array())
 * @method static int                                    countByTeam_group_id($val, array<string, mixed> $opt=array())
 * @method static int                                    countByLiga_shortname($val, array<string, mixed> $opt=array())
 * @method static int                                    countByLiga_name($val, array<string, mixed> $opt=array())
 * @method static int                                    countByProvider($val, array<string, mixed> $opt=array())
 * @method static int                                    countByVerband($val, array<string, mixed> $opt=array())
 * @method static int                                    countByHandballnet_team_id($val, array<string, mixed> $opt=array())
 * @method static int                                    countByHandballnet_tournament_id($val, array<string, mixed> $opt=array())
 * @method static int                                    countByTournament_type($val, array<string, mixed> $opt=array())
 * @method static int                                    countByAge_group($val, array<string, mixed> $opt=array())
 * @method static int                                    countByMy_team_name($val, array<string, mixed> $opt=array())
 * @method static int                                    countByIs_active($val, array<string, mixed> $opt=array())
 */
class HandballnetTeamsModel extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_hn_teams';

    public function getProvider(): Provider|null
    {
        return Provider::tryFrom($this->provider);
    }

    public function getVerband(): Verband|null
    {
        return Verband::tryFrom($this->verband);
    }

    public function getAgeGroup(): AgeGroup|null
    {
        return AgeGroup::tryFrom($this->age_group);
    }
}
