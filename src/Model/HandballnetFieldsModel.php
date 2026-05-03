<?php

declare(strict_types=1);

namespace Janborg\H4aTabellen\Model;

use Contao\Model;

/**
 * Reads and writes Handballnet Fields
 * 
 * @property int $id
 * @property int $tstamp
 * @property string $fieldId
 * @property string $acronym
 * @property string $name
 * @property string $city
 * @property string $fieldNumber
 * 
 * @method static HandballnetFieldsModel|null             findById($id, array $opt=array())
 * @method static HandballnetFieldsModel|null             findOneBy($col, $val, array $opt=array())
 * @method static HandballnetFieldsModel|null             findOneByFieldId($val, array $opt=array())
 * @method static HandballnetFieldsModel|null             findOneByFieldNumber($val, array $opt=array())
 */
class HandballnetFieldsModel extends Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected static $strTable = 'tl_handballnet_fields';
}
