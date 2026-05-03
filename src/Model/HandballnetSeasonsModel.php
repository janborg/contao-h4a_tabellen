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

/**
 * Reads and writes handballnetTeams.
 *
 * @property int    $id
 * @property int    $tstamp
 * @property string $season_id
 * @property string $season_name
 * @property string $club_name
 * @property string $provider
 * @property string $verband
 * @property string $handballnet_club_id
 * @property bool   $is_active
 *
 * @method static HandballnetSeasonsModel|null             findById($id, array $opt=array())
 * @method static HandballnetSeasonsModel|null             findOneBy($col, $val, array $opt=array())
 * @method static HandballnetSeasonsModel|null             findOneByHandballnet_club_id($val, array $opt=array())
 * @method static Collection<HandballnetSeasonsModel>|null findBySeason_id($val, array $opt=array())
 * @method static Collection<HandballnetSeasonsModel>|null findBySeason_name($val, array $opt=array())
 * @method static Collection<HandballnetSeasonsModel>|null findByClub_name($val, array $opt=array())
 * @method static Collection<HandballnetSeasonsModel>|null findByProvider($val, array $opt=array())
 * @method static Collection<HandballnetSeasonsModel>|null findByVerband($val, array $opt=array())
 * @method static Collection<HandballnetSeasonsModel>|null findByIs_active($val, array $opt=array())
 */

class HandballnetSeasonsModel extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_hn_seasons';
}
