<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

use Janborg\H4aTabellen\HandballNet\Verband;
use Janborg\H4aTabellen\HandballNet\Provider;
use Janborg\H4aTabellen\Controller\ContentElement\HandballnetTabelleElement;
use Janborg\H4aTabellen\Controller\ContentElement\HandballnetSpielplanElement;
use Janborg\H4aTabellen\Controller\ContentElement\HandballnetWidgetElement;

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

/*
 * Palettes
 */

$GLOBALS['TL_DCA']['tl_content']['palettes'][HandballnetSpielplanElement::TYPE] = '
    {type_legend},type,headline;
    {handballnet_legend},handballnet_club,handballnet_season,handballnet_team_id;
    {template_legend:hide},customTpl;
    {expert_legend:hide},cssID
';
$GLOBALS['TL_DCA']['tl_content']['palettes'][HandballnetTabelleElement::TYPE] ='
    {type_legend},type,headline;
    {handballnet_legend},handballnet_club,handballnet_season,handballnet_tournament_id;
    {template_legend:hide},customTpl;
    {expert_legend:hide},cssID
';
$GLOBALS['TL_DCA']['tl_content']['palettes'][HandballnetWidgetElement::TYPE] = '
    {type_legend},type;
    {widget_legend},hn_widget_type;
    {handballnet_legend},handballnet_club,handballnet_season,handballnet_team_id;
    {expert_legend:hide},cssID
';
    

/*
 * Fields
 */

$GLOBALS['TL_DCA']['tl_content']['fields']['handballnet_club'] = [
    'foreignKey' => 'tl_hn_clubs.name',
    'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
    'inputType' => 'select',
    'eval' => [
        'mandatory' => true,
        'tl_class' => 'w50',
        'includeBlankOption' => true,
        'chosen' => true,
        'submitOnChange' => true,
    ],
    'sql' => "varchar(10) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['handballnet_season'] = [
    'inputType' => 'select',
    'eval' => [
        'mandatory' => true,
        'tl_class' => 'w50',
        'includeBlankOption' => true,
        'chosen' => true,
        'submitOnChange' => true,
    ],
    'sql' => "varchar(10) NOT NULL default ''",
];
$GLOBALS['TL_DCA']['tl_content']['fields']['h4a_liga_ID'] = [
    'inputType' => 'text',
    'eval' => [
        'mandatory' => true,
        'rgxp' => 'digit',
        'minlenght' => 5,
        'maxlength' => 6,
        'tl_class' => 'w50',
    ],
    'sql' => "varchar(255) NOT NULL default ''",
];
$GLOBALS['TL_DCA']['tl_content']['fields']['h4a_team_ID'] = [
    'inputType' => 'text',
    'eval' => [
        'mandatory' => true,
        'rgxp' => 'digit',
        'minlenght' => 6,
        'maxlength' => 7,
        'tl_class' => 'w50',
    ],
    'sql' => "varchar(255) NOT NULL default ''",
];
$GLOBALS['TL_DCA']['tl_content']['fields']['h4a_verein_ID'] = [
    'inputType' => 'text',
    'eval' => [
        'mandatory' => true,
        'rgxp' => 'digit',
        'minlenght' => 1,
        'maxlength' => 4,
        'tl_class' => 'w50',
    ],
    'sql' => "varchar(255) NOT NULL default ''",
];
$GLOBALS['TL_DCA']['tl_content']['fields']['my_team_name'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['my_team_name'],
    'inputType' => 'text',
    'eval' => [
        'mandatory' => true,
        'unique' => false,
        'maxlength' => 255,
        'tl_class' => 'w50',
    ],
    'sql' => "varchar(255) NOT NULL default ''",
];
$GLOBALS['TL_DCA']['tl_content']['fields']['provider'] = [
    'inputType' => 'select',
    'sorting' => true,
    'filter' => true,
    'enum' => Provider::class,
    'eval' => [
        'mandatory' => true,
        'maxlength' => 255,
        'tl_class' => 'w50',
        'includeBlankOption' => true,
        'chosen' => true,
    ],
    'sql' => "varchar(255) NOT NULL default ''",
];
$GLOBALS['TL_DCA']['tl_content']['fields']['verband'] = [
    'inputType' => 'select',
    'sorting' => true,
    'filter' => true,
    'enum' => Verband::class,
    'eval' => [
        'mandatory' => true,
        'maxlength' => 255,
        'tl_class' => 'w50',
        'includeBlankOption' => true,
        'chosen' => true,
    ],
    'sql' => "varchar(255) NOT NULL default ''",
];
$GLOBALS['TL_DCA']['tl_content']['fields']['team_id'] = [
    'inputType' => 'text',
    'eval' => [
        'mandatory' => true,
        'rgxp' => 'digit',
        'maxlength' => 7,
        'tl_class' => 'w50',
    ],
    'sql' => "varchar(10) NOT NULL default ''",
];
$GLOBALS['TL_DCA']['tl_content']['fields']['handballnet_team_id'] = [
    'inputType' => 'select',
    'eval' => [
        'mandatory' => true,
        'includeBlankOption' => true,
        'chosen' => true,
        'tl_class' => 'w50',
    ],
    'sql' => "varchar(255) NOT NULL default ''",
];
$GLOBALS['TL_DCA']['tl_content']['fields']['handballnet_tournament_id'] = [
    'inputType' => 'select',
    'eval' => [
        'mandatory' => false,
        'includeBlankOption' => true,
        'maxlength' => 255,
        'tl_class' => 'w50',
    ],
    'sql' => "varchar(255) NOT NULL default ''",
];
$GLOBALS['TL_DCA']['tl_content']['fields']['hn_widget_type'] = [
    'exclude'                 => true,
    'sorting'                 => true,
    'inputType'               => 'select',
    'options'                 => ['spielplan', 'tabelle', 'club'],
    'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50'),
    'sql'                     => "varchar(255) NOT NULL default ''"
];
$GLOBALS['TL_DCA']['tl_content']['fields']['hn_widget_type'] = [
    'exclude'                 => true,
    'sorting'                 => true,
    'inputType'               => 'select',
    'options'                 => ['spielplan', 'tabelle', 'club'],
    'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50'),
    'sql'                     => "varchar(255) NOT NULL default ''"
];