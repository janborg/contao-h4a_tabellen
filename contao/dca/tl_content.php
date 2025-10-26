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
use Janborg\H4aTabellen\Controller\ContentElement\H4aTabelleElement;
use Janborg\H4aTabellen\Controller\ContentElement\H4aSpielplanElement;
use Janborg\H4aTabellen\Controller\ContentElement\HandballnetTabelleElement;
use Janborg\H4aTabellen\Controller\ContentElement\HandballnetSpielplanElement;

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

$GLOBALS['TL_DCA']['tl_content']['palettes'][H4aTabelleElement::TYPE] = '{type_legend},type,headline;{h4a_legend},h4a_liga_ID, my_team_name;{template_legend:hide},customTpl;{expert_legend:hide},cssID';
$GLOBALS['TL_DCA']['tl_content']['palettes'][H4aSpielplanElement::TYPE] = '{type_legend},type,headline;{h4a_legend},h4a_team_ID, my_team_name;{template_legend:hide},customTpl;{expert_legend:hide},cssID';
//TODO: select via Season Data
$GLOBALS['TL_DCA']['tl_content']['palettes'][HandballnetSpielplanElement::TYPE] = '{type_legend,type,headline;{handballnet_legend},handballnet_saison,handballnet_team_id,my_team_name;{template_legend:hide},customTpl;{expert_legend:hide},cssID';
$GLOBALS['TL_DCA']['tl_content']['palettes'][HandballnetTabelleElement::TYPE] ='{type_legend,type,headline;{handballnet_legend},handballnet_saison,handballnet_tournament_id,my_team_name;{template_legend:hide}';
/*
 * Fields
 */

$GLOBALS['TL_DCA']['tl_content']['fields']['handballnet_saison'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['handballnet_saison'],
    'inputType' => 'select',
    'foreignKey' => 'tl_h4a_seasons.season',
    'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
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
    'label' => &$GLOBALS['TL_LANG']['tl_content']['h4a_liga_ID'],
    'inputType' => 'text',
    'exclude' => true,
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
    'label' => &$GLOBALS['TL_LANG']['tl_content']['h4a_team_ID'],
    'inputType' => 'text',
    'exclude' => true,
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
    'label' => &$GLOBALS['TL_LANG']['tl_content']['h4a_verein_ID'],
    'inputType' => 'text',
    'exclude' => true,
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
    'exclude' => true,
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
    'exclude' => true,
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
    'exclude' => true,
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
    'label' => &$GLOBALS['TL_LANG']['tl_calendar']['team_id'],
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
    'label' => &$GLOBALS['TL_LANG']['tl_content']['handballnet_team_id'],
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
    'label' => &$GLOBALS['TL_LANG']['tl_calendar']['handballnet_id'],
    'inputType' => 'text',
    'eval' => [
        'mandatory' => false,
        'maxlength' => 255,
        'tl_class' => 'w50',
    ],
    'sql' => "varchar(255) NOT NULL default ''",
];