<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

namespace Janborg\H4aTabellen\Helper;

use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\System;
use Janborg\H4aTabellen\Model\H4aJsonDataModel;
use Psr\Log\LogLevel;
use Symfony\Component\DomCrawler\Crawler;

class Helper
{
    /**
     * @param string $type 'class' oder 'team' oder 'club' oder 'score'
     * @param int    $id
     *
     * @return string
     */
    public static function getURL($type, $id)
    {
        switch ($type) {
            case 'class_table':
                $liga_url = 'https://api.h4a.mobi/spo/spo-proxy_public.php?cmd=data&lvTypeNext=class&subType=table&lvIDNext='.$id;
                break;

            case 'class_games':
                $liga_url = 'https://api.h4a.mobi/spo/spo-proxy_public.php?cmd=data&lvTypeNext=class&lvIDNext='.$id;
                break;

            case 'team':
                $liga_url = 'https://api.h4a.mobi/spo/spo-proxy_public.php?cmd=data&lvTypeNext=team&lvIDNext='.$id;
                break;

            case 'club':
                $liga_url = 'https://api.h4a.mobi/spo/spo-proxy_public.php?cmd=data&lvTypeNext=club&lvIDNext='.$id;
                break;

            case 'score':
                $liga_url = 'https://spo.handball4all.de/Spielbetrieb/index.php?orgGrpID=1&all=1&score='.$id;
                break;
        }

        return $liga_url;
    }

    /**
     * @param int $teamID
     *
     * @return array
     */
    public static function getJsonSpielplan($teamID)
    {
        $type = 'team';

        $arrResult = null;

        $liga_url = self::getURL($type, $teamID);

        $strJson = self::file_get_contents_ssl($liga_url);

        try {
            $arrResult = json_decode($strJson, true);
        } catch (\Exception $e) {
            System::getContainer()
                ->get('monolog.logger.contao')
                ->log(LogLevel::INFO, 'Json File für team_id '.$teamID.' konnte nicht erstellt werden!', ['contao' => new ContaoContext(__CLASS__.'::'.__FUNCTION__, TL_ERROR)])
            ;
        }

        return $arrResult[0];
    }

    /**
     * @param int $ligaID
     *
     * @return array
     */
    public static function getJsonLigaSpielplan($ligaID)
    {
        $type = 'class_games';

        $arrResult = null;

        $liga_url = self::getURL($type, $ligaID);

        $strJson = self::file_get_contents_ssl($liga_url);

        try {
            $arrResult = json_decode($strJson, true);
        } catch (\Exception $e) {
            System::getContainer()
                ->get('monolog.logger.contao')
                ->log(LogLevel::INFO, 'Json File für liga_id '.$ligaID.' konnte nicht erstellt werden!', ['contao' => new ContaoContext(__CLASS__.'::'.__FUNCTION__, TL_ERROR)])
            ;
        }

        return $arrResult[0];
    }

    /**
     * @param int $ligaID
     *
     * @return array
     */
    public static function getJsonTabelle($ligaID)
    {
        $type = 'class_table';

        $arrResult = null;

        $liga_url = self::getURL($type, $ligaID);

        $strJson = self::file_get_contents_ssl($liga_url);

        try {
            $arrResult = json_decode($strJson, true);
        } catch (\Exception $e) {
            System::getContainer()
                ->get('monolog.logger.contao')
                ->log(LogLevel::INFO, 'Json File für liga_id '.$ligaID.' konnte nicht abgerufen werden!', ['contao' => new ContaoContext(__CLASS__.'::'.__FUNCTION__, TL_ERROR)])
            ;
        }

        return $arrResult[0];
    }

    /**
     * @param int $vereinID
     *
     * @return arary
     */
    public static function getJsonVerein($vereinID)
    {
        $type = 'club';

        $arrResult = null;

        $liga_url = self::getURL($type, $vereinID);

        $strJson = self::file_get_contents_ssl($liga_url);

        try {
            $arrResult = json_decode($strJson, true);
        } catch (\Exception $e) {
            System::getContainer()
                ->get('monolog.logger.contao')
                ->log(LogLevel::INFO, 'Json File für liga_id '.$vereinID.' konnte nicht abgerufen werden!', ['contao' => new ContaoContext(__CLASS__.'::'.__FUNCTION__, TL_ERROR)])
            ;
        }

        return $arrResult[0];
    }

    /**
     * @param array $arrResultSpielplan
     * @param array $arrResultTabelle
     */
    public static function updateDatabaseFromJsonFile($arrResultSpielplan, $arrResultTabelle): void
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
                    $objH4aJsonData->season = date('Y', $Unixdate - 31536000).'/'.date('Y', $Unixdate);
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

    /**
     * Ermittelt die Nummer des Reports (URL Parameter sGID)).
     *
     * @param string $ligaID
     * @param string $gameNo
     *
     * @return string
     */
    public static function getReportNo($ligaID, $gameNo)
    {
        $url = self::getURL('score', $ligaID);

        $html = self::file_get_contents_ssl($url);

        $crawler = new Crawler($html);

        $crawler = $crawler->filterXPath('//table[@class="gametable"]/tr[position() > 1]');

        $allGames = $crawler->filterXPath('//tr')->each(
            static function ($tr, $i) {
                return $tr->filterXPath('//td')->each(
                    static function ($td, $i) {
                        $value['text'] = $td->text();

                        if ($td->filterXPath('//a')->count() > 0 && null !== $td->filterXPath('//a')->attr('href')) {
                            $parts = parse_url($td->filterXPath('//a')->attr('href'));
                            parse_str($parts['query'], $query);

                            if (isset($query['sGID'])) {
                                $value['sGID'] = $query['sGID'];
                            }
                        }

                        return $value;
                    }
                );
            }
        );

        foreach ($allGames as $game) {
            if ($game[1]['text'] === $gameNo) {
                $sGID = $game[10]['sGID'];
            }
        }

        return $sGID;
    }
    
    public static function file_get_contents_ssl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3000); // 3 sec.
        curl_setopt($ch, CURLOPT_TIMEOUT, 10000); // 10 sec.
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}
