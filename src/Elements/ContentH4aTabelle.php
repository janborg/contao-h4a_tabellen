<?php

/*
 * Copyright (C) 2017 Janborg
 */

namespace Janborg\H4aTabellen\Elements;

use StringUtil;
use Symfony\Component\Config\Definition\Exception\Exception;
use System;


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
        if (TL_MODE == 'BE') {
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
        $this->Template->wildcard = 'liga_ID: '.$this->h4a_liga_ID.', Team ID: '.$this->h4a_team_ID.', Team Name: '.$this->my_team_name;
    }

    /**
     * Erzeugt die Ausgabe für das Frontend.
     *
     * @return string
     */
    private function genFeOutput()
    {
        //json File des Teams abrufen
        $liga_url = 'https://h4a.it4sport.de/spo/spo-proxy_public.php?cmd=data&lvTypeNext=class&subType=table&lvIDNext='.$this->h4a_liga_ID;

        // prepare cache control
        $strCachePath = StringUtil::stripRootDir(System::getContainer()->getParameter('kernel.cache_dir'));
        $arrResult = null;
        $strCacheFile = $strCachePath . '/contao/janborg/' . $this->h4a_liga_ID . '.json';

		// Load the cached result
        if (file_exists(TL_ROOT . '/' . $strCacheFile))
        {
            $objFile = new \File($strCacheFile);
            if ($objFile->mtime > time() - 60*60*6)
            {
                $arrResult = json_decode($objFile->getContent(), true);
            }
            else
            {
                $objFile->delete();
            }
        }

		// Cache the result
        if ($arrResult === null)
        {
            try
            {
                $arrResult = json_decode(file_get_contents($liga_url), true);
            }
            catch (\Exception $e)
            {
                System::log('h4a update failed: ' . $e->getMessage(), __METHOD__, TL_ERROR);
                $arrResult = array();
            }
            \File::putContent($strCacheFile, json_encode($arrResult));
        }


        // Template ausgeben
        $this->Template = new \FrontendTemplate($this->strTemplate);
        $this->Template->class = 'ce_h4a_tabelle';
        $this->Template->teams = $arrResult[0]['dataList'];
        $this->Template->myTeam = $this->my_team_name;
    }
}
