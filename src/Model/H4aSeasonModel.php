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

/**
 * add properties for IDE support.
 *
 * @property string $hash
 *
 * @method static H4aSeasonModel|null findById($id, array $opt=array())
 */
class H4aSeasonModel extends Model
{
    protected static $strTable = 'tl_h4a_seasons';

    // if you have logic you need more often, you can implement it here
}
