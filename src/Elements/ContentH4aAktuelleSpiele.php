<?php

/*
 * This file is part of contao-h4a_tabellen.
 * (c) Jan Lünborg
 * @license MIT
 */

namespace Janborg\H4aTabellen\Elements;

use Contao\BackendTemplate;
use Contao\ContentElement;
use Contao\FrontendTemplate;
use Janborg\H4aTabellen\Helper\Helper;

/**
 * Class ContentH4aAktuelleSpiele.
 *
 * @author Janborg
 */
class ContentH4aAktuelleSpiele extends ContentElement
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'ce_h4a_aktuellespiele';

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
        $this->Template->wildcard = 'Verein ID: '.$this->h4a_verein_ID.', Team Name: '.$this->my_team_name;
    }

    /**
     * Erzeugt die Ausgabe für das Frontend.
     *
     * @return string
     */
    private function genFeOutput()
    {
        $arrResult = Helper::getJsonVerein($this->h4a_verein_ID);
        $lastUpdate = time();

        // Template ausgeben
        $this->Template = new FrontendTemplate($this->strTemplate);
        $this->Template->class = 'ce_h4a_aktuellespiele';
        $this->Template->spiele = $arrResult['dataList'];
        $this->Template->myTeam = $this->my_team_name;
        $this->Template->lastUpdate = $lastUpdate;
    }
}
