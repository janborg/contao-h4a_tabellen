<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

use Contao\Backend;
use Contao\BackendUser;
use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\StringUtil;

/*
 * Global Operation(s)
 */

$GLOBALS['TL_DCA']['tl_calendar']['list']['global_operations'] = array_merge(
    ['h4a_update' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar']['operationImportFromH4a'],
        'class' => 'header_h4a',
        'href' => 'key=update_events',
        'icon' => 'bundles/janborgh4atabellen/update.svg',
        'button_callback' => ['tl_calendar_h4a', 'h4a_update_events'],
    ]],
    ['h4a_update_results' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar']['operationUpdateResultsFromH4a'],
        'class' => 'header_h4a',
        'href' => 'key=update_results',
        'icon' => 'bundles/janborgh4atabellen/update.svg',
        'button_callback' => ['tl_calendar_h4a', 'h4a_update_results'],
    ]],
    ['h4a_seasons' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar']['operationSeasons'],
        'class' => 'header_h4a',
        'href' => 'table=tl_h4a_seasons',
        'icon' => 'bundles/janborgh4atabellen/seasons.svg',
        'attr' => 'onclick="Backend.getScrollOffset()"',
    ]],
    $GLOBALS['TL_DCA']['tl_calendar']['list']['global_operations']
);

/*
 * Operation(s)
 */
 $GLOBALS['TL_DCA']['tl_calendar']['list']['operations'] = array_merge(
     $GLOBALS['TL_DCA']['tl_calendar']['list']['operations'],
     ['toggle_ignore' => [
         'label' => &$GLOBALS['TL_LANG']['tl_calendar']['toggle_ignore'],
         'attributes' => 'onclick="Backend.getScrollOffset();"',
         'haste_ajax_operation' => [
             'field' => 'h4a_ignore',
             'options' => [
                 [
                     'value' => '',
                     'icon' => 'visible.gif',
                 ],
                 [
                     'value' => '1',
                     'icon' => 'delete.gif',
                 ],
             ],
         ],
     ]]
 );

/*
 * Extend palettes
 */
PaletteManipulator::create()
    ->addLegend('h4a_legend', 'title_legend', PaletteManipulator::POSITION_AFTER)
    ->addField('h4a_imported', 'h4a_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_calendar')
;

/*
 * Add Selector(s)
 */

$GLOBALS['TL_DCA']['tl_calendar']['palettes']['__selector__'] = array_merge(
    ['h4a_imported',
    ],
    $GLOBALS['TL_DCA']['tl_calendar']['palettes']['__selector__']
);

/*
 * Create Subpalette(s)
 */

$GLOBALS['TL_DCA']['tl_calendar']['subpalettes'] = array_merge(
    ['h4a_imported' => 'h4a_seasons, h4aEvents_author, h4a_ignore',
    ],
    $GLOBALS['TL_DCA']['tl_calendar']['subpalettes']
);

/*
 * Table tl_calendar
 */
$GLOBALS['TL_DCA']['tl_calendar']['fields'] = array_merge(
    ['h4a_imported' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar']['h4a_imported'],
        'exclude' => true,
        'filter' => true,
        'inputType' => 'checkbox',
        'eval' => ['submitOnChange' => true],
        'sql' => "char(1) NOT NULL default ''",
    ]],
    ['h4a_ignore' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar']['h4a_ignore'],
        'exclude' => true,
        'filter' => true,
        'inputType' => 'checkbox',
        'eval' => ['tl_class' => 'w50 m12'],
        'sql' => "char(1) NOT NULL default ''",
    ]],
    ['h4a_seasons' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar']['h4a_seasons'],
        'exclude' => true,
        'inputType' => 'multiColumnWizard',
        'eval' => [
            'style' => 'width: 680px;',
            'dragAndDrop'  => true,
            'buttons'      => [
                'copy'   => false,
                'up'     => true,
                'down'   => true
            ],
            'columnFields' => [
                'h4a_saison' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_calendar']['h4a_saison'],
                    'inputType' => 'select',
                    'foreignKey' => 'tl_h4a_seasons.season',
                    'eval' => [
                        'mandatory' => true,
                        'style' => 'width:150px;',
                        'includeBlankOption' => true,
                        'chosen' => true,
                    ],
                ],
                'h4a_team' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_calendar']['h4a_team'],
                    'inputType' => 'text',
                    'eval' => [
                        'mandatory' => true,
                        'rgxp' => 'digit',
                        'maxlength' => 6,
                        'style' => 'width:100px;',
                    ],
                ],
                'h4a_liga' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_calendar']['h4a_liga_ID'],
                    'inputType' => 'text',
                    'eval' => [
                        'mandatory' => true,
                        'rgxp' => 'digit',
                        'maxlength' => 5,
                        'style' => 'width:100px;',
                    ],
                ],
                'my_team_name' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_calendar']['my_team_name'],
                    'inputType' => 'text',
                    'eval' => [
                        'mandatory' => true,
                        'maxlength' => 255,
                        'style' => 'width:250px;',
                    ],
                ],
            ],
        ],
        'sql' => 'blob NULL',
    ]],
    ['h4aEvents_author' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar']['h4aEvents_author'],
        'default' => BackendUser::getInstance()->id,
        'exclude' => true,
        'filter' => true,
        'sorting' => true,
        'flag' => 1,
        'inputType' => 'select',
        'foreignKey' => 'tl_user.name',
        'eval' => [
            'doNotCopy' => true,
            'chosen' => true,
            'mandatory' => true,
            'includeBlankOption' => true,
            'cols' => 4,
            'tl_class' => 'w50',
        ],
        'sql' => "int(10) unsigned NOT NULL default '0'",
        'relation' => [
            'type' => 'hasOne',
            'load' => 'eager',
        ],
    ]],
    $GLOBALS['TL_DCA']['tl_calendar']['fields']
);

/**
 * Class tl_calendar_h4a.
 */
class tl_calendar_h4a extends Backend
{
    /**
     * tl_calendar_h4a constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->import('BackendUser', 'User');
    }

    /**
     * @param $href
     * @param $label
     * @param $title
     * @param $class
     * @param $attributes
     *
     * @return string
     */
    public function h4a_update_events($href, $label, $title, $class, $attributes)
    {
        $href = 'key=update_events';

        return '<a href="'.$this->addToUrl($href).'" title="'.StringUtil::specialchars($title).'" class="'.$class.'"'.$attributes.'>'.$label.'</a> ';
    }

    /**
     * @param $href
     * @param $label
     * @param $title
     * @param $class
     * @param $attributes
     *
     * @return string
     */
    public function h4a_update_results($href, $label, $title, $class, $attributes)
    {
        $href = 'key=update_results';

        return '<a href="'.$this->addToUrl($href).'" title="'.StringUtil::specialchars($title).'" class="'.$class.'"'.$attributes.'>'.$label.'</a> ';
    }
}
