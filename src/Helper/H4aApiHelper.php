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

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

final class H4aApiHelper
{
    private string $cmd = 'data';

    private string $lvTypeNext;

    private string $lvIDNext;

    private string $subType = 'table';

    private string $request_url;

    public function __construct(
        private $baseUrl = 'https://api.h4a.mobi/spo/spo-proxy_public.php',
    ) {
    }

    /**
     * @param string $lvIDNext
     *
     * @return H4aApiHelper
     */
    public function setLvIDNext($lvIDNext)
    {
        $this->lvIDNext = $lvIDNext;

        return $this;
    }

    public function getSpielplanForTeamID()
    {
        $this->setLvTypeNext('team');

        $this->request_url = $this->baseUrl.'?cmd='.$this->cmd.'&lvTypeNext='.$this->lvTypeNext.'&lvIDNext='.$this->lvIDNext;

        $response = $this->getResponse();

        return $response[0];
    }

    public function getSpielplanForClassID()
    {
        $this->setLvTypeNext('class');

        $this->request_url = $this->baseUrl.'?cmd='.$this->cmd.'&lvTypeNext='.$this->lvTypeNext.'&lvIDNext='.$this->lvIDNext;

        $response = $this->getResponse();

        return $response[0];
    }

    public function getSpielplanForClubID()
    {
        $this->setLvTypeNext('club');

        $this->request_url = $this->baseUrl.'?cmd='.$this->cmd.'&lvTypeNext='.$this->lvTypeNext.'&lvIDNext='.$this->lvIDNext;

        $response = $this->getResponse();

        return $response[0];
    }

    public function getTabelleForClassID()
    {
        $this->setLvTypeNext('class');

        $this->request_url = $this->baseUrl.'?cmd='.$this->cmd.'&lvTypeNext='.$this->lvTypeNext.'&subType='.$this->subType.'&lvIDNext='.$this->lvIDNext;

        $response = $this->getResponse();

        return $response[0];
    }

    /**
     * Ermittelt die Nummer des Reports (URL Parameter sGID)).
     *
     * @param string $ligaID
     * @param string $gameNo
     *
     * @return string|null
     */
    public function getReportNo($ligaID, $gameNo)
    {
        $this->setbaseUrl('https://spo.handball4all.de/Spielbetrieb/index.php?orgGrpID=1&all=1&score=');

        $httpClient = HttpClient::create();

        $response = $httpClient->request(
            'GET',
            $this->baseUrl.$ligaID,
        )
            ->getContent()
        ;

        $crawler = new Crawler($response);

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
     * @param string $baseUrl
     *
     * @return H4aApiHelper
     */
    private function setbaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    /**
     * @param string $cmd
     *
     * @return H4aApiHelper
     */
    private function setCmd($cmd)
    {
        $this->cmd = $cmd;

        return $this;
    }

    /**
     * @param string $lvTypeNext
     *
     * @return H4aApiHelper
     */
    private function setLvTypeNext($lvTypeNext)
    {
        $this->lvTypeNext = $lvTypeNext;

        return $this;
    }

    /**
     * @param string $subType
     *
     * @return H4aApiHelper
     */
    private function setSubType($subType)
    {
        $this->subType = $subType;

        return $this;
    }

    private function getResponse(): array
    {
        $httpClient = HttpClient::create();

        $response = $httpClient->request(
            'GET',
            $this->request_url,
        );

        $statusCode = $response->getStatusCode();

        $contentType = $response->getHeaders()['content-type'][0];

        return $response->toArray();
    }

    public function getH4ateamFromH4aSeasons(CalendarModel $objCalendar, CalendarEventsModel $objEvent): string
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
