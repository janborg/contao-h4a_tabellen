<?php

/*
 * This file is part of contao-h4a_tabellen.
 * (c) Jan Lünborg
 * @license LGPL-3.0-or-later
 */

namespace Janborg\H4aTabellen\Elements;

use Janborg\H4aTabellen\Helper\Helper;

/**
 * Class ContentH4aTabelle.
 *
 * @author Janborg
 */
class ContentH4aTabelle extends \ContentElement
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'ce_h4a_tabelle';

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
        $this->Template = new \BackendTemplate($this->strTemplate);
        $this->Template->title = $this->headline;
        $this->Template->wildcard = 'liga_ID: '.$this->h4a_liga_ID.', Team Name: '.$this->my_team_name;
    }

    /**
     * Erzeugt die Ausgabe für das Frontend.
     *
     * @return string
     */
    private function genFeOutput()
    {
        $type = 'liga';
        $liga_url = Helper::getURL($type, $this->h4a_liga_ID);
        $strCacheFile = Helper::getCachedFile($this->h4a_liga_ID);
        $cacheTime = $GLOBALS['TL_CONFIG']['h4a_cache_time'];

        // Load the cached result
        if (file_exists(TL_ROOT.'/'.$strCacheFile)) {
            $objFile = new \File($strCacheFile);
            if ($objFile->mtime > time() - $cacheTime) {
                $arrResult = json_decode($objFile->getContent(), true);
                $lastUpdate = $objFile->mtime;
            }
        }

        // Cache the result
        if (null === $arrResult) {
            $arrResult = Helper::setCachedFile($this->h4a_liga_ID, $liga_url);
        }

        // Template ausgeben
        $this->Template = new \FrontendTemplate($this->strTemplate);
        $this->Template->class = 'ce_h4a_tabelle';
        $this->Template->teams = $arrResult[0]['dataList'];
        $this->Template->myTeam = $this->my_team_name;
        $this->Template->lastUpdate = $lastUpdate;
    }
}
