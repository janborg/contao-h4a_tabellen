<?php
/**
 * Table tl_h4ajsondata
 */
$GLOBALS ['TL_DCA'] ['tl_h4ajsondata'] =
[
    // Config
    'config' => [
        'dataContainer'               => 'Table',
        'sql' => array(
            'keys'  => array(
                'id'    => 'primary'
            )
        ),
    ],
    'list' => array(
        'sorting' => array(
            'mode'                    => 1,
            'flag'                    => 12,
            'fields'                  => array('season'),
            'panelLayout'             => 'search, sort;filter,limit'
        ),
        'label' => array(
            'fields'                  => array('gClassName', 'lvTypeLabelStr'),
            'format'                  => '%s - %s'
            ),
        
        'global_operations' => array(
            'all' => array(
                'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'                => 'act=select',
                'class'               => 'header_edit_all',
                'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            )
        ),
        'operations' => array(
            'edit' => array(
                'label'               => &$GLOBALS['TL_LANG']['tl_h4ajsondata']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'edit.svg'
            ),
            'delete' => array(
                'label'               => &$GLOBALS['TL_LANG']['tl_h4ajsondata']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.svg',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
            ),
            'show' => array(
                'label'               => &$GLOBALS['TL_LANG']['tl_h4ajsondata']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.svg'
            ),
        )
    ),
    // Palettes
    'palettes' => array(
      'default' => '{title_legend}, lvTypePathStr, lvIDPathStr, lvTypeLabelStr, gClassID, gClassName, DateStart, season, gClassNameLong, gTableJson, gTeamJson'
    ),
    // Fields
    'fields' => array(
        'id' => array(
            'sql'                     => "int(10) unsigned NOT NULL auto_increment"
        ),
        'tstamp' => array(
            'sql'                     => "int(10) unsigned NOT NULL default '0'"
        ),
        'lvTypePathStr' => array(
            'label'                   => &$GLOBALS['TL_LANG']['tl_h4ajsondata']['lvTypePathStr'],
            'exclude'                 => true,
            'inputType'               => 'select',
            'options'                 => array('class', 'team'),
            'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'lvIDPathStr' => array(
            'label'                   => &$GLOBALS['TL_LANG']['tl_h4ajsondata']['lvIDPathStr'],
            'exclude'                 => true,
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>true, 'maxlength'=>6, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'lvTypeLabelStr'=> array(
            'label'                   => &$GLOBALS['TL_LANG']['tl_h4ajsondata']['lvTypeLabelStr'],
            'exclude'                 => true,
            'sorting'                 => true,
            'search'                  => true,
            'filter'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'gClassID' => array(
            'label'                   => &$GLOBALS['TL_LANG']['tl_h4ajsondata']['gClassID'],
            'exclude'                 => true,
            'sorting'                 => true,
            'filter'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NULL default ''"
        ),
        'gClassName' => array(
            'label'                   => &$GLOBALS['TL_LANG']['tl_h4ajsondata']['gClassName'],
            'exclude'                 => true,
            'sorting'                 => true,
            'filter'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NULL default ''"
        ),
        'DateStart' => array(
            'label'                   => &$GLOBALS['TL_LANG']['tl_h4ajsondata']['DateStart'],
            'exclude'                 => true,
            'sorting'                 => true,
            'inputType'               => 'text',
            'eval'                    => array('rgxp'=>'date', 'tl_class'=>'w50', 'datepicker'=>true),
            'sql'                     => "varchar(10) NULL default ''"
        ),
        'season' => array(
            'label'                   => &$GLOBALS['TL_LANG']['tl_h4ajsondata']['season'],
            'exclude'                 => true,
            'sorting'                 => true,
            'inputType'               => 'text',
            'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NULL default ''"
        ),
        'gClassNameLong' => array(
            'label'                   => &$GLOBALS['TL_LANG']['tl_h4ajsondata']['gClassNameLong'],
            'exclude'                 => true,
            'sorting'                 => true,
            'inputType'               => 'text',
            'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NULL default ''"
        ),
        'gTableJson' => array(
            'label'                   => &$GLOBALS['TL_LANG']['tl_h4ajsondata']['gTableJson'],
            'exclude'                 => true,
            'sorting'                 => false,
            'inputType'               => 'textarea',
            'eval'                    => array('tl_class'=>'clr'),
            'sql'                     => "text NOT NULL default ''"
        ),
        'gTeamJson' => array(
            'label'                   => &$GLOBALS['TL_LANG']['tl_h4ajsondata']['gTeamJson'],
            'exclude'                 => true,
            'sorting'                 => false,
            'inputType'               => 'textarea',
            'eval'                    => array('tl_class'=>'clr'),
            'sql'                     => "text NOT NULL default ''"
        ),
    )
];
