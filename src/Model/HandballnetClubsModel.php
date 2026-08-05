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
 * Reads and writes handballnetClubs.
 *
 * @property int    $id
 * @property int    $tstamp
 * @property string $handballnet_id
 * @property string $name
 * @property string $acronym
 * @property string $logo
 * @property string $org_id
 * @property string $org_name
 * @property string $org_acronym
 * @property bool   $is_active
 *
 * @method static HandballnetClubsModel|null             findById($id, array<string, mixed> $opt=array())
 * @method static HandballnetClubsModel|null             findByPk($id, array<string, mixed> $opt=array())
 * @method static HandballnetClubsModel|null             findByIdOrAlias($val, array<string, mixed> $opt=array())
 * @method static HandballnetClubsModel|null             findOneBy($col, $val, array<string, mixed> $opt=array())
 * @method static HandballnetClubsModel|null             findOneByHandballnet_id($val, array<string, mixed> $opt=array())
 * @method static HandballnetClubsModel|null             findOneByName($val, array<string, mixed> $opt=array())
 * @method static HandballnetClubsModel|null             findOneByAcronym($val, array<string, mixed> $opt=array())
 * @method static HandballnetClubsModel|null             findOneByLogo($val, array<string, mixed> $opt=array())
 * @method static HandballnetClubsModel|null             findOneByOrg_id($val, array<string, mixed> $opt=array())
 * @method static HandballnetClubsModel|null             findOneByOrg_name($val, array<string, mixed> $opt=array())
 * @method static HandballnetClubsModel|null             findOneByOrg_acronym($val, array<string, mixed> $opt=array())
 * @method static HandballnetClubsModel|null             findOneByIs_active($val, array<string, mixed> $opt=array())
 * @method static Collection<HandballnetClubsModel>|null findMultipleByIds(array<int, int|string> $ids, array<string, mixed> $opt=array())
 * @method static Collection<HandballnetClubsModel>|null findBy($col, $val, array<string, mixed> $opt=array())
 * @method static Collection<HandballnetClubsModel>|null findByHandballnet_id($val, array<string, mixed> $opt=array())
 * @method static Collection<HandballnetClubsModel>|null findByName($val, array<string, mixed> $opt=array())
 * @method static Collection<HandballnetClubsModel>|null findByAcronym($val, array<string, mixed> $opt=array())
 * @method static Collection<HandballnetClubsModel>|null findByLogo($val, array<string, mixed> $opt=array())
 * @method static Collection<HandballnetClubsModel>|null findByOrg_id($val, array<string, mixed> $opt=array())
 * @method static Collection<HandballnetClubsModel>|null findByOrg_name($val, array<string, mixed> $opt=array())
 * @method static Collection<HandballnetClubsModel>|null findByOrg_acronym($val, array<string, mixed> $opt=array())
 * @method static Collection<HandballnetClubsModel>|null findByIs_active($val, array<string, mixed> $opt=array())
 * @method static Collection<HandballnetClubsModel>|null findAll(array<string, mixed> $opt=array())
 * @method static int                                    countById($id, array<string, mixed> $opt=array())
 * @method static int                                    countByHandballnet_id($val, array<string, mixed> $opt=array())
 * @method static int                                    countByName($val, array<string, mixed> $opt=array())
 * @method static int                                    countByAcronym($val, array<string, mixed> $opt=array())
 * @method static int                                    countByLogo($val, array<string, mixed> $opt=array())
 * @method static int                                    countByOrg_id($val, array<string, mixed> $opt=array())
 * @method static int                                    countByOrg_name($val, array<string, mixed> $opt=array())
 * @method static int                                    countByOrg_acronym($val, array<string, mixed> $opt=array())
 * @method static int                                    countByIs_active($val, array<string, mixed> $opt=array())
 */
class HandballnetClubsModel extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_hn_clubs';
}
