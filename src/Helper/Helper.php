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

use Contao\CalendarEventsModel;
use Contao\CalendarModel;
use Janborg\H4aTabellen\Model\H4aJsonDataModel;
use Symfony\Component\DomCrawler\Crawler;

class Helper
{
    final public const CLASS_TABLE_URL = 'https://api.h4a.mobi/spo/spo-proxy_public.php?cmd=data&lvTypeNext=class&subType=table&lvIDNext=';

    final public const CLASS_GAMES_URL = 'https://api.h4a.mobi/spo/spo-proxy_public.php?cmd=data&lvTypeNext=class&lvIDNext=';

    final public const TEAM_GAMES_URL = 'https://api.h4a.mobi/spo/spo-proxy_public.php?cmd=data&lvTypeNext=team&lvIDNext=';

    final public const CLUB_GAMES_URL = 'https://api.h4a.mobi/spo/spo-proxy_public.php?cmd=data&lvTypeNext=club&lvIDNext=';

    final public const GAME_SCORES_URL = 'https://spo.handball4all.de/Spielbetrieb/index.php?orgGrpID=1&all=1&score=';

    /**
     * @param string $type 'class' oder 'team' oder 'club' oder 'score'
     * @param string $id
     */
    public static function getURL($type, $id): string
    {
        return match ($type) {
            'class_table' => 'https://api.h4a.mobi/spo/spo-proxy_public.php?cmd=data&lvTypeNext=class&subType=table&lvIDNext='.$id,
            'class_games' => 'https://api.h4a.mobi/spo/spo-proxy_public.php?cmd=data&lvTypeNext=class&lvIDNext='.$id,
            'team' => 'https://api.h4a.mobi/spo/spo-proxy_public.php?cmd=data&lvTypeNext=team&lvIDNext='.$id,
            'club' => 'https://api.h4a.mobi/spo/spo-proxy_public.php?cmd=data&lvTypeNext=club&lvIDNext='.$id,
            'score' => 'https://spo.handball4all.de/Spielbetrieb/index.php?orgGrpID=1&all=1&score='.$id,
            default => '',
        };
    }

    /**
     * @param string $teamID
     *
     * @return array<mixed>
     */
    public static function getJsonSpielplan($teamID)
    {
        $arrResult = null;

        $liga_url = self::TEAM_GAMES_URL.$teamID;

        $strJson = self::file_get_contents_ssl($liga_url);

        $arrResult = json_decode($strJson, true);

        return $arrResult[0];
    }

    /**
     * @param string $ligaID
     *
     * @return array<mixed>
     */
    public static function getJsonLigaSpielplan($ligaID)
    {
        $arrResult = null;

        $liga_url = self::CLASS_GAMES_URL.$ligaID;

        $strJson = self::file_get_contents_ssl($liga_url);

        $arrResult = json_decode($strJson, true);

        return $arrResult[0];
    }

    /**
     * @param string $ligaID
     *
     * @return array<mixed>
     */
    public static function getJsonTabelle($ligaID)
    {
        $arrResult = null;

        $liga_url = self::CLASS_TABLE_URL.$ligaID;

        $strJson = self::file_get_contents_ssl($liga_url);

        $arrResult = json_decode($strJson, true);

        return $arrResult[0];
    }

    /**
     * @param string $vereinID
     *
     * @return array<mixed>
     */
    public static function getJsonVerein($vereinID)
    {
        $arrResult = null;

        $liga_url = self::CLUB_GAMES_URL.$vereinID;

        $strJson = self::file_get_contents_ssl($liga_url);

        $arrResult = json_decode($strJson, true);

        return $arrResult[0];
    }

    /**
     * @param array<mixed> $arrResultSpielplan
     * @param array<mixed> $arrResultTabelle
     */
    public static function updateDatabaseFromJsonFile($arrResultSpielplan, $arrResultTabelle): void
    {
        $objH4aJsonData = H4aJsonDataModel::findOneBy(
            ['lvTypePathStr=?', 'lvIDPathStr=?'],
            [$arrResultSpielplan['lvTypePathStr'], $arrResultSpielplan['lvIDPathStr']],
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
                    $objH4aJsonData->season = date('Y', $Unixdate).'/'.date('Y', $Unixdate + 31536000);
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
     * @return string|null
     */
    public static function getReportNo($ligaID, $gameNo)
    {
        $url = self::GAME_SCORES_URL.$ligaID;

        $html = self::file_get_contents_ssl($url);

        $crawler = new Crawler($html);

        $crawler = $crawler->filterXPath('//table[@class="gametable"]/tr[position() > 1]');

        $allGames = $crawler->filterXPath('//tr')->each(
            static fn ($tr, $i) => $tr->filterXPath('//td')->each(
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
                },
            ),
        );

        $game = array_filter(
            $allGames,
            static fn ($game) => $game[1]['text'] === $gameNo,
        );
        $game = array_values($game);

        return $game[0][10]['sGID'] ?? null;
    }

    /**
     * @param string $url
     */
    public static function file_get_contents_ssl($url): string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3000); // 3 sec.
        curl_setopt($ch, CURLOPT_TIMEOUT, 10000); // 10 sec.
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    public static function getH4ateamFromH4aSeasons(CalendarModel $objCalendar, CalendarEventsModel $objEvent): string
    {
        $arrSeasons = unserialize($objCalendar->h4a_seasons);

        $season = array_filter(
            $arrSeasons,
            static fn ($season) => $season['h4a_saison'] === $objEvent->h4a_season,
        );

        $season = array_values($season);

        return $season[0]['h4a_team'];
    }
}
