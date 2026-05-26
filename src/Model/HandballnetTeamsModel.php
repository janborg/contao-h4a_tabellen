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
use Janborg\H4aTabellen\HandballNet\AgeGroup;
use Janborg\H4aTabellen\HandballNet\Provider;
use Janborg\H4aTabellen\HandballNet\Verband;

/**
 * Reads and writes handballnetTeams.
 *
 * @property int      $id
 * @property int      $pid
 * @property int      $tstamp
 * @property string   $saison
 * @property string   $team_logo
 * @property string   $team_group_id
 * @property string   $liga_shortname
 * @property string   $liga_name
 * @property string   $provider
 * @property string  $verband
 * @property string   $handballnet_team_id
 * @property string   $handballnet_tournament_id
 * @property string   $tournament_type
 * @property string $ageGroup
 * @property string   $my_team_name
 * @property bool     $is_active
 *
 * @method static HandballnetTeamsModel|null             findById($id, array $opt=array())
 * @method static HandballnetTeamsModel|null             findByPk($id, array $opt=array())
 * @method static HandballnetTeamsModel|null             findOneBy($col, $val, array $opt=array())
 * @method static HandballnetTeamsModel|null             findByPid($val, array $opt=array())
 * @method static HandballnetTeamsModel|null             findOneByHandballnet_team_id($val, array $opt=array())
 * @method static HandballnetTeamsModel|null             findOneByHandballnet_tournament_id($val, array $opt=array())
 * @method static HandballnetTeamsModel|null             findOneByLiga_shortname($val, array $opt=array())
 * @method static HandballnetTeamsModel|null             findOneByLiga_name($val, array $opt=array())
 * @method static HandballnetTeamsModel|null             findOneByProvider($val, array $opt=array())
 * @method static HandballnetTeamsModel|null             findOneByVerband($val, array $opt=array())
 * @method static HandballnetTeamsModel|null             findOneByMy_team_name($val, array $opt=array())
 * @method static Collection<HandballnetTeamsModel>|null findByPid($val, array $opt=array())
 * @method static Collection<HandballnetTeamsModel>|null findByMy_team_name($val, array $opt=array())
 * @method static Collection<HandballnetTeamsModel>|null findByProvider($val, array $opt=array())
 * @method static Collection<HandballnetTeamsModel>|null findByVerband($val, array $opt=array())
 * @method static Collection<HandballnetTeamsModel>|null findByIs_active($val, array $opt=array())
 */
class HandballnetTeamsModel extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_hn_teams';

    public function getProvider(): ?Provider
    {
        return Provider::tryFrom($this->provider);
    }

    public function getVerband(): ?Verband
    {
        return Verband::tryFrom($this->verband);
    }

    public function getAgeGroup(): ?AgeGroup
    {
        return AgeGroup::tryFrom($this->age_group);
    }
}
