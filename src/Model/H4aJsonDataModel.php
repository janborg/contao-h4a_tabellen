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
 * @method static H4aDataModel|null findById($id, array $opt=array())
 */
class H4aJsonDataModel extends Model
{
    protected static $strTable = 'tl_h4ajsondata';

    // if you have logic you need more often, you can implement it here
}
