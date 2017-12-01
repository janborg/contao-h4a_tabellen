<?php

/* 
 * Copyright (C) 2015 Janborg
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Contao;

/**
 * Class ContentHVWTabelle
 *
 * @author Janborg
 */

class ContentH4aTabelle extends \ContentElement{

    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'ce_h4a_tabelle';


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
     * Erzeugt die Ausgebe für das Backend.
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
     * Erzeugt die Ausgabe für das Frontend.
     * @return string
     */
    private function genFeOutput()
	{
	    //json File für Team abholen
		$liga_url = 'https://h4a.it4sport.de/spo/spo-proxy_public.php?cmd=data&lvTypeNext=class&subType=table&lvIDNext='.$this->h4a_liga_ID;
		$strTeamsJson = file_get_contents($liga_url);
		
		//json File in Array umwandeln
		$arrTeams = json_decode($strTeamsJson, true);
		
        // Template ausgeben
        $this->Template = new \FrontendTemplate($this->strTemplate);
        $this->Template->class="ce_h4a_tabelle";
        $this->Template->teams=$arrTeams[0]['dataList'];
        $this->Template->myTeam=$this->my_team_name;
    }
}
