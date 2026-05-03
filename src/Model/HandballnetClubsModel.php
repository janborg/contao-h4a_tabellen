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
 * @property string $handballnet_id
 * @property string $name
 * @property string $acronym
 * @property string $org_id
 * @property string $org_name
 * @property string $org_acronym
 * @property bool   $is_active
 *
 * @method static HandballnetClubsModel|null             findById($id, array $opt=array())
 * @method static HandballnetClubsModel|null             findOneBy($col, $val, array $opt=array())
 * @method static HandballnetClubsModel|null             findOneByHandballnet_id($val, array $opt=array())
 * @method static Collection<HandballnetClubsModel>|null findByName($val, array $opt=array())
 * @method static Collection<HandballnetClubsModel>|null findByAcronym($val, array $opt=array())
 * @method static Collection<HandballnetClubsModel>|null findByOrg_id($val, array $opt=array())
 * @method static Collection<HandballnetClubsModel>|null findByOrg_Name($val, array $opt=array())
 * @method static Collection<HandballnetClubsModel>|null findByOrg_acronym($val, array $opt=array())
 * @method static Collection<HandballnetClubsModel>|null findByIs_active($val, array $opt=array())
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
