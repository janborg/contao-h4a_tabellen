<?php

/*
 * Copyright (C) 2017 Janborg
 *
 */

namespace Janborg\H4aTabellen\Resources\Elements;

use Contao\ContentElement;

/**
 * Class ContentHVWSpiele
 *
 * @author Janborg
 */

class ContentH4aSpiele extends \ContentElement{

    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'ce_h4a_spiele';

    /**
     * Generate the module
     */
    protected function compile()
	{
		if (TL_MODE == 'BE') {
            $this->genBeOutput();
        } else {
            $this->genFeOutput();
        }
    }

    /**
     * Erzeugt die Ausgebe f�r das Backend.
     * @return string
     */
    private function genBeOutput()
    {
        $this->strTemplate          = 'be_wildcard';
        $this->Template             = new \BackendTemplate($this->strTemplate);
        $this->Template->title      = $this->headline;
        $this->Template->wildcard   = "liga_ID: ".$this->h4a_liga_ID.", Team ID: ".$this->h4a_team_ID.", Team Name: ".$this->my_team_name;
    }
      /**
     * Erzeugt die Ausgabe f�r das Frontend.
     * @return string
     */
    private function genFeOutput()
	{
		//json File f�r Team abholen
		$liga_url = 'https://h4a.it4sport.de/spo/spo-proxy_public.php?cmd=data&lvTypeNext=team&lvIDNext='.$this->h4a_team_ID;
		$strSpieleJson = file_get_contents($liga_url);

		//json File in Array umwandeln
		$arrSpiele = json_decode($strSpieleJson, true);

        // Template ausgeben
        $this->Template = new \FrontendTemplate($this->strTemplate);
        $this->Template->class="ce_h4a_spiele";
        $this->Template->spiele=$arrSpiele[0]['dataList'];
        $this->Template->myTeam=$this->my_team_name;
    }
}
