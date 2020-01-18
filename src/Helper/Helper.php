<?php

/*
 * This file is part of contao-h4a_tabellen.
 * (c) Jan Lünborg
 * @license LGPL-3.0-or-later
 */

namespace Janborg\H4aTabellen\Helper;

use StringUtil;
use System;

class Helper
{
    /**
     * @param string $type 'liga' oder 'team' oder 'verein'
     * @param int    $id
     *
     * @return string $liga_url
     */
    public static function getURL($type, $id)
    {
        if ('liga' === $type) {
            $liga_url = 'https://api.h4a.mobi/spo/spo-proxy_public.php?cmd=data&lvTypeNext=class&subType=table&lvIDNext='.$id;
        }
        if ('team' === $type) {
            $liga_url = 'https://api.h4a.mobi/spo/spo-proxy_public.php?cmd=data&lvTypeNext=team&lvIDNext='.$id;
        }
        if ('verein' === $type) {
            $liga_url = 'https://api.h4a.mobi/spo/spo-proxy_public.php?cmd=data&lvTypeNext=club&lvIDNext='.$id;
        }

        return $liga_url;
    }

    /**
     * @param int $id
     *
     * @return $strCacheFile
     */
    public static function getCachedFile($id)
    {
        // prepare cache control
        $strCachePath = StringUtil::stripRootDir(System::getContainer()->getParameter('kernel.cache_dir'));
        $arrResult = null;
        $strCacheFile = $strCachePath.'/contao/janborg/'.$id.'.json';

        return $strCacheFile;
    }

    /**
     * @param int    $id
     * @param string $liga_url
     *
     * @return $arrResult
     */
    public static function setCachedFile($id, $liga_url)
    {
        // prepare cache control
        $strCachePath = StringUtil::stripRootDir(System::getContainer()->getParameter('kernel.cache_dir'));
        $arrResult = null;
        $strCacheFile = $strCachePath.'/contao/janborg/'.$id.'.json';
        $strJson = file_get_contents($liga_url);

        try {
            $arrResult = json_decode($strJson, true);
        } catch (\Exception $e) {
            System::log('h4a update failed for h4a-ID: '.$id.$e->getMessage(), __METHOD__, TL_ERROR);
        }
        if (null === $arrResult) {
            return $arrResult;
        }
        \File::putContent($strCacheFile, json_encode($arrResult));

        return $arrResult;
    }
}
