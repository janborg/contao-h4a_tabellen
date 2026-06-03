<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

use Contao\DC_Table;
use Contao\DataContainer;

$GLOBALS['TL_DCA']['tl_hn_clubs'] = [
    // Config
    'config' => [
        'dataContainer' => DC_Table::class,
        'switchToEdit'                => true,
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
        'ctable' => ['tl_hn_seasons'],
        'backlink' => 'do=handballnet_teams'
    ],
    'list' => [
        'sorting' => [
            'mode' => DataContainer::MODE_SORTED,
            'flag' => DataContainer::SORT_INITIAL_LETTER_DESC,
            'fields' => ['name'],
            'panelLayout' => 'search;filter;limit',
        ],
        'label' => [
            'fields' => ['name', 'handballnet_id', 'org_name'],
            'format' => '%s (%s) | %s',
        ],
        'global_operations' => [
            'all',
        ],
        'operations' => [
            'edit',
            'children',
            'delete',
            'toggle' => [
				'href'                => 'act=toggle&amp;field=is_active',
				'icon'                => 'bundles/janborgh4atabellen/refresh.svg',
                'primary'             => true
			],
            'show',
        ],
    ],
    // Palettes
    'palettes' => [
        'default' => '{title_legend},handballnet_id,name,acronym,logo;{organization_legend},org_id, org_name, org_acronym;{status_legend}, is_active',
    ],
    // Fields
    'fields' => [
        'id' => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'tstamp' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'handballnet_id' => [
            'inputType' => 'text',
            'exclude' => true,
            'sorting' => true,
            'search' => true,
            'eval' => [
                'mandatory' => true,
                'alwaysSave' => true,
                'maxlength' => 255,
                'tl_class' => 'w50 clr',
            ],
            'sql' => "varchar(255) unsigned NOT NULL default ''",
        ],
        'name' => [
            'inputType' => 'text',
            'exclude' => true,
            'sorting' => true,
            'filter' => true,
            'search' => true,
            'eval' => [
                'mandatory' => false,
                'maxlength' => 255,
                'tl_class' => 'w50',
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'acronym' => [
            'inputType' => 'text',
            'exclude' => true,
            'sorting' => true,
            'filter' => true,
            'search' => true,
            'eval' => [
                'mandatory' => false,
                'maxlength' => 255,
                'tl_class' => 'w50',
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ], 
        'logo' => [
			'inputType'               => 'fileTree',
			'eval'                    => [
                'filesOnly'=>true, 
                'fieldType'=>'radio',
                'mandatory'=>false, 
                'tl_class'=>'clr',
            ],
//			'load_callback' => [
//				['tl_content', 
//                'setSingleSrcFlags'],
//			],
			'sql'                     => "binary(16) NULL"
        ],
        'org_id' => [
            'inputType' => 'text',
            'exclude' => true,
            'sorting' => true,
            'filter' => true,
            'search' => true,
            'eval' => [
                'mandatory' => false,
                'maxlength' => 255,
                'tl_class' => 'w50',
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'org_name' => [
            'inputType' => 'text',
            'exclude' => true,
            'sorting' => true,
            'filter' => true,
            'search' => true,
            'eval' => [
                'mandatory' => false,
                'maxlength' => 255,
                'tl_class' => 'w50',
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'org_acronym' => [
            'inputType' => 'text',
            'exclude' => true,
            'sorting' => true,
            'filter' => true,
            'search' => true,
            'eval' => [
                'mandatory' => false,
                'maxlength' => 255,
                'tl_class' => 'w50',
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'is_active' => [
            'toggle' => true,
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 m12'],
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
    ],
];
