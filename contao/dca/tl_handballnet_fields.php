<?php

declare(strict_types=1);

use Contao\DataContainer;
use Contao\DC_Table;

$GLOBALS['TL_DCA']['tl_handballnet_fields'] = [
    'config' => [
        'dataContainer'    => DC_Table::class,
        'enableVersioning' => false,
        'notCopyable'      => true,
        'sql' => [
            'keys' => [
                'id'       => 'primary',
                'fieldId' => 'unique',
            ],
        ],
    ],

    'list' => [
        'sorting' => [
            'mode'        => DataContainer::MODE_SORTABLE,
            'flag'        => DataContainer::SORT_INITIAL_LETTER_ASC,
            'fields'      => ['name ASC'],
            'panelLayout' => 'search,limit',
        ],
        'label' => [
            'fields' => ['name', 'city', 'field_number'],
            'format' => '%s &nbsp;(%s) &nbsp;– Nr. %s',
        ],
        'global_operations' => [],
        'operations' => [
            'show' => [
                'href' => 'act=show',
                'icon' => 'show.svg',
            ],
        ],
    ],

    'fields' => [
        'id' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'autoincrement' => true],
        ],
        'tstamp' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
        ],
        'fieldId' => [
            'inputType' => 'text',
            'sorting'   => true,
            'filter'    => true,
            'eval'      => ['readonly' => true, 'tl_class' => 'w50'],
            'sql'       => ['type' => 'string', 'length' => 100, 'default' => ''],
        ],
        'name' => [
            'inputType' => 'text',
            'eval'      => ['readonly' => true, 'tl_class' => 'w50'],
            'sql'       => ['type' => 'string', 'length' => 200, 'default' => ''],
        ],
        'acronym' => [
            'inputType' => 'text',
            'eval'      => ['readonly' => true, 'tl_class' => 'w50'],
            'sql'       => ['type' => 'string', 'length' => 50, 'default' => ''],
        ],
        'city' => [
            'inputType' => 'text',
            'eval'      => ['readonly' => true, 'tl_class' => 'w50'],
            'sql'       => ['type' => 'string', 'length' => 100, 'default' => ''],
        ],
        'fieldNumber' => [
            'inputType' => 'text',
            'eval'      => ['readonly' => true, 'tl_class' => 'w50'],
            'sql'       => ['type' => 'string', 'length' => 20, 'default' => ''],
        ],
    ],
];
