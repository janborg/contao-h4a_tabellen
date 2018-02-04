<?php

namespace Janborg\H4aTabellen\Helper;

use StringUtil;
use Symfony\Component\Config\Definition\Exception\Exception;
use System;

class Helper
{
    public static function getURL($type,$id)
    {
        if ($type=='liga') {
            $liga_url = 'https://h4a.it4sport.de/spo/spo-proxy_public.php?cmd=data&lvTypeNext=class&subType=table&lvIDNext='.$id;
        }
        if ($type=='team') {
            $liga_url = 'https://h4a.it4sport.de/spo/spo-proxy_public.php?cmd=data&lvTypeNext=team&lvIDNext='.$id;
        }

        return $liga_url;
    }
    public static function getCachedFile($id)
    {
        // prepare cache control
        $strCachePath = StringUtil::stripRootDir(System::getContainer()->getParameter('kernel.cache_dir'));
        $arrResult = null;
        $strCacheFile = $strCachePath . '/contao/janborg/' . $id . '.json';

        return $strCacheFile;
    }
    public static function setCachedFile($id,$liga_url)
    {
        // prepare cache control
        $strCachePath = StringUtil::stripRootDir(System::getContainer()->getParameter('kernel.cache_dir'));
        $arrResult = null;
        $strCacheFile = $strCachePath . '/contao/janborg/' . $id . '.json';
        try
        {
            $arrResult = json_decode(file_get_contents($liga_url), true);
        }
        catch (\Exception $e)
        {
            System::log('h4a update failed for h4a-ID: '.$id . $e->getMessage(), __METHOD__, TL_ERROR);
            $arrResult = array();
        }
        \File::putContent($strCacheFile, json_encode($arrResult));

        return $arrResult;
    }
}
