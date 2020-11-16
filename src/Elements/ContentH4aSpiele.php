<?php

/*
 * This file is part of contao-h4a_tabellen.
 * (c) Jan Lünborg
 * @license MIT
 */

namespace Janborg\H4aTabellen\Elements;

use Janborg\H4aTabellen\Helper\Helper;
use Contao\BackendTemplate;
use Contao\FrontendTemplate;
use Contao\ContentElement;


/**
 * Class ContentH4aSpiele.
 *
 * @author Janborg
 */
class ContentH4aSpiele extends ContentElement
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'ce_h4a_spiele';

    /**
     * Generate the module.
     */
    protected function compile()
    {
        if (TL_MODE === 'BE') {
            $this->genBeOutput();
        } else {
            $this->genFeOutput();
        }
    }

    /**
     * Erzeugt die Ausgebe für das Backend.
     *
     * @return string
     */
    private function genBeOutput()
    {
        $this->strTemplate = 'be_wildcard';
        $this->Template = new BackendTemplate($this->strTemplate);
        $this->Template->title = $this->headline;
        $this->Template->wildcard = 'Team ID: '.$this->h4a_team_ID.', Team Name: '.$this->my_team_name;
    }

    /**
     * Erzeugt die Ausgabe für das Frontend.
     *
     * @return string
     */
    private function genFeOutput()
    {

        $arrResult = Helper::getJsonSpielplan($this->h4a_team_ID);
        $lastUpdate = time();

        // Template ausgeben
        $this->Template = new FrontendTemplate($this->strTemplate);
        $this->Template->class = 'ce_h4a_spiele';
        $this->Template->spiele = $arrResult['dataList'];
        $this->Template->myTeam = $this->my_team_name;
        $this->Template->lastUpdate = $lastUpdate;
    }
}
