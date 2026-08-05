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
 * Reads and writes handballnetSeasons for Clubs.
 *
 * @property int    $id
 * @property int    $pid
 * @property int    $tstamp
 * @property string $season_name
 * @property string $season_id
 * @property string $club_name
 * @property int    $handballnet_club_id
 * @property bool   $is_active
 *
 * @method static HandballnetSeasonsModel|null             findById($id, array<string, mixed> $opt=array())
 * @method static HandballnetSeasonsModel|null             findByPk($id, array<string, mixed> $opt=array())
 * @method static HandballnetSeasonsModel|null             findByIdOrAlias($val, array<string, mixed> $opt=array())
 * @method static HandballnetSeasonsModel|null             findOneBy($col, $val, array<string, mixed> $opt=array())
 * @method static HandballnetSeasonsModel|null             findOneByPid($val, array<string, mixed> $opt=array())
 * @method static HandballnetSeasonsModel|null             findOneBySeason_name($val, array<string, mixed> $opt=array())
 * @method static HandballnetSeasonsModel|null             findOneBySeason_id($val, array<string, mixed> $opt=array())
 * @method static HandballnetSeasonsModel|null             findOneByClub_name($val, array<string, mixed> $opt=array())
 * @method static HandballnetSeasonsModel|null             findOneByHandballnet_club_id($val, array<string, mixed> $opt=array())
 * @method static HandballnetSeasonsModel|null             findOneByIs_active($val, array<string, mixed> $opt=array())
 * @method static Collection<HandballnetSeasonsModel>|null findMultipleByIds($ids, array<string, mixed> $opt=array())
 * @method static Collection<HandballnetSeasonsModel>|null findBy($col, $val, array<string, mixed> $opt=array())
 * @method static Collection<HandballnetSeasonsModel>|null findByPid($val, array<string, mixed> $opt=array())
 * @method static Collection<HandballnetSeasonsModel>|null findBySeason_name($val, array<string, mixed> $opt=array())
 * @method static Collection<HandballnetSeasonsModel>|null findBySeason_id($val, array<string, mixed> $opt=array())
 * @method static Collection<HandballnetSeasonsModel>|null findByClub_name($val, array<string, mixed> $opt=array())
 * @method static Collection<HandballnetSeasonsModel>|null findByHandballnet_club_id($val, array<string, mixed> $opt=array())
 * @method static Collection<HandballnetSeasonsModel>|null findByIs_active($val, array<string, mixed> $opt=array())
 * @method static Collection<HandballnetSeasonsModel>|null findAll(array<string, mixed> $opt=array())
 * @method static int                                      countById($id, array<string, mixed> $opt=array())
 * @method static int                                      countByPid($val, array<string, mixed> $opt=array())
 * @method static int                                      countBySeason_name($val, array<string, mixed> $opt=array())
 * @method static int                                      countBySeason_id($val, array<string, mixed> $opt=array())
 * @method static int                                      countByClub_name($val, array<string, mixed> $opt=array())
 * @method static int                                      countByHandballnet_club_id($val, array<string, mixed> $opt=array())
 * @method static int                                      countByIs_active($val, array<string, mixed> $opt=array())
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
