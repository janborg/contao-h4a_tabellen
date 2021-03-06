<?php

/*
 * This file is part of contao-h4a_tabellen.
 * (c) Jan Lünborg
 * @license MIT
 */

namespace Janborg\H4aTabellen\Helper;

use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\File;
use Contao\StringUtil;
use Contao\System;
use Janborg\H4aTabellen\Model\H4aJsonDataModel;
use Psr\Log\LogLevel;

class Helper
{
    /**
     * @param string $type 'class' oder 'team' oder 'club'
     * @param int    $id
     *
     * @return string $liga_url
     */
    public static function getURL($type, $id)
    {
        if ('class' === $type) {
            $liga_url = 'https://api.h4a.mobi/spo/spo-proxy_public.php?cmd=data&lvTypeNext=class&subType=table&lvIDNext='.$id;
        }
        if ('team' === $type) {
            $liga_url = 'https://api.h4a.mobi/spo/spo-proxy_public.php?cmd=data&lvTypeNext=team&lvIDNext='.$id;
        }
        if ('club' === $type) {
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
            System::getContainer()
            ->get('monolog.logger.contao')
            ->log(LogLevel::INFO, 'h4a update failed for h4a-ID: '.$id.$e->getMessage(), ['contao' => new ContaoContext(__CLASS__.'::'.__FUNCTION__, TL_ERROR)]);
        }
        if (null === $arrResult) {
            return $arrResult;
        }
        File::putContent($strCacheFile, json_encode($arrResult));

        return $arrResult;
    }

    /**
     * @param int $teamID
     *
     * @return $arrResult
     */
    public static function getJsonSpielplan($teamID)
    {
        $type = 'team';

        $arrResult = null;

        $liga_url = self::getURL($type, $teamID);

        // in cache speichern
        $strCachePath = StringUtil::stripRootDir(System::getContainer()->getParameter('kernel.cache_dir'));
        $strFilePath = $strCachePath.'/contao/janborg/'.$teamID.'.json';

        $strJson = file_get_contents($liga_url);

        try {
            $arrResult = json_decode($strJson, true);
        } catch (\Exception $e) {
            System::getContainer()
            ->get('monolog.logger.contao')
            ->log(LogLevel::INFO, 'Json File für team_id '.$teamID.' konnte nicht erstellt werden!', ['contao' => new ContaoContext(__CLASS__.'::'.__FUNCTION__, TL_ERROR)]);
        }

        File::putContent($strFilePath, json_encode($arrResult));

        return $arrResult[0];
    }

    /**
     * @param int $ligaID
     *
     * @return $arrResult
     */
    public static function getJsonTabelle($ligaID)
    {
        $type = 'class';

        $arrResult = null;

        $liga_url = self::getURL($type, $ligaID);

        $strCachePath = StringUtil::stripRootDir(System::getContainer()->getParameter('kernel.cache_dir'));
        $strFilePath = $strCachePath.'/contao/janborg/'.$ligaID.'.json';

        $strJson = file_get_contents($liga_url);

        try {
            $arrResult = json_decode($strJson, true);
        } catch (\Exception $e) {
            System::getContainer()
            ->get('monolog.logger.contao')
            ->log(LogLevel::INFO, 'Json File für liga_id '.$ligaID.' konnte nicht abgerufen werden!', ['contao' => new ContaoContext(__CLASS__.'::'.__FUNCTION__, TL_ERROR)]);
        }

        File::putContent($strFilePath, json_encode($arrResult));

        return $arrResult[0];
    }

    /**
     * @param int $vereinID
     *
     * @return $arrResult
     */
    public static function getJsonVerein($vereinID)
    {
        $type = 'club';

        $arrResult = null;

        $liga_url = self::getURL($type, $vereinID);

        $strCachePath = StringUtil::stripRootDir(System::getContainer()->getParameter('kernel.cache_dir'));
        $strFilePath = $strCachePath.'/contao/janborg/'.$vereinID.'.json';

        $strJson = file_get_contents($liga_url);

        try {
            $arrResult = json_decode($strJson, true);
        } catch (\Exception $e) {
            System::getContainer()
            ->get('monolog.logger.contao')
            ->log(LogLevel::INFO, 'Json File für liga_id '.$vereinID.' konnte nicht abgerufen werden!', ['contao' => new ContaoContext(__CLASS__.'::'.__FUNCTION__, TL_ERROR)]);
        }

        File::putContent($strFilePath, json_encode($arrResult));

        return $arrResult[0];
    }

    /**
     * @param array $arrResultSpielplan
     * @param array $arrResultTabelle
     */
    public static function updateDatabaseFromJsonFile($arrResultSpielplan, $arrResultTabelle)
    {
        $objH4aJsonData = H4aJsonDataModel::findOneBy(
            ['lvTypePathStr=?', 'lvIDPathStr=?'],
            [$arrResultSpielplan['lvTypePathStr'], $arrResultSpielplan['lvIDPathStr']]
        );

        if (null === $objH4aJsonData) {
            $objH4aJsonData = new H4aJsonDataModel();
        }

        $objH4aJsonData->tstamp = time();
        $objH4aJsonData->lvTypePathStr = $arrResultSpielplan['lvTypePathStr'];
        $objH4aJsonData->lvIDPathStr = $arrResultSpielplan['lvIDPathStr'];
        $objH4aJsonData->lvTypeLabelStr = trim($arrResultSpielplan['lvTypeLabelStr'], '/ ');
        $objH4aJsonData->gClassID = $arrResultSpielplan['dataList'][0]['gClassID'];
        $objH4aJsonData->gClassName = $arrResultSpielplan['dataList'][0]['gClassSname'];
        $objH4aJsonData->gTeamJson = json_encode($arrResultSpielplan);
        $objH4aJsonData->gTableJson = json_encode($arrResultTabelle);

        if (null !== $arrResultSpielplan['dataList'][0]['gDate']) {
            $arrDate = explode('.', $arrResultSpielplan['dataList'][0]['gDate']);
            $Unixdate = mktime(0, 0, 0, (int) $arrDate[1], (int) $arrDate[0], (int) $arrDate[2]);
            $objH4aJsonData->DateStart = $Unixdate;
            $month = date('m', $Unixdate);

            switch (true) {
                case $month < 4:
                    $objH4aJsonData->season = date('Y', $Unixdate - 31536000).'/'.(date('Y', $Unixdate));
                    break;

                case $month >= 3:
                    $objH4aJsonData->season = date('Y', $Unixdate).'/'.(date('Y', $Unixdate + 31536000));
                    break;
            }
        }

        if (null !== $arrResultTabelle['lvTypeLabelStr']) {
            $objH4aJsonData->gClassNameLong = trim($arrResultTabelle['lvTypeLabelStr'], '/ ');
        }

        $objH4aJsonData->save();
    }
}
