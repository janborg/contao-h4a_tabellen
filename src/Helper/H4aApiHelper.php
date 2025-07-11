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
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class H4aApiHelper
{
    private string $cmd = 'data';

    private string $lvTypeNext;

    private string $lvIDNext;

    private string $subType = 'table';

    private string $request_url;

    public function __construct(
        private string $baseUrl,
        private readonly HttpClientInterface $httpClient,
        private readonly CacheInterface $appCache,
        private readonly LoggerInterface $contaoLogger,
        private int $h4aCacheTtl,
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

    /**
     * @param bool $cache
     *
     * @return array<mixed>
     */
    public function getSpielplanForTeamID($cache = true)
    {
        $this->setLvTypeNext('team');

        $this->request_url = $this->baseUrl.'?cmd='.$this->cmd.'&lvTypeNext='.$this->lvTypeNext.'&lvIDNext='.$this->lvIDNext;

        $response = $this->getResponse($cache);

        return $response[0];
    }

    /**
     * @param bool $cache
     *
     * @return array<mixed>
     */
    public function getSpielplanForClassID($cache = true)
    {
        $this->setLvTypeNext('class');

        $this->request_url = $this->baseUrl.'?cmd='.$this->cmd.'&lvTypeNext='.$this->lvTypeNext.'&lvIDNext='.$this->lvIDNext;

        $response = $this->getResponse($cache);

        return $response[0];
    }

    /**
     * @param bool $cache
     *
     * @return array<mixed>
     */
    public function getSpielplanForClubID($cache = true)
    {
        $this->setLvTypeNext('club');

        $this->request_url = $this->baseUrl.'?cmd='.$this->cmd.'&lvTypeNext='.$this->lvTypeNext.'&lvIDNext='.$this->lvIDNext;

        $response = $this->getResponse($cache);

        return $response[0];
    }

    /**
     * @param bool $cache
     *
     * @return array<mixed>
     */
    public function getTabelleForClassID($cache = true)
    {
        $this->setLvTypeNext('class');

        $this->request_url = $this->baseUrl.'?cmd='.$this->cmd.'&lvTypeNext='.$this->lvTypeNext.'&subType='.$this->subType.'&lvIDNext='.$this->lvIDNext;

        $response = $this->getResponse($cache);

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

        $response = $this->httpClient->request(
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

        return $game[0][10]['sGID'] ?? '';
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
     * @return array<mixed>
     */
    private function getResponse(bool $cache): array
    {
        $cacheKey = md5($this->request_url);

        if (!$cache) {
            $this->appCache->delete($cacheKey);
        }

        return $this->appCache->get(
            $cacheKey,
            function (ItemInterface $item) {
                $item->expiresAfter($this->h4aCacheTtl);

                try {
                    $response = $this->httpClient->request(
                        'GET',
                        $this->request_url,
                    );

                    if (empty($response->getContent())) {
                        // Handle the empty response body
                        $item->expiresAfter(0);

                        return [
                            0 => [
                                'dataList' => '',
                            ],
                        ];
                    }

                    return $response->toArray();
                } catch (TransportExceptionInterface|HttpExceptionInterface $e) {
                    $this->contaoLogger->error(\sprintf('Unable to get data from "%s": %s', $this->request_url, $e->getMessage()));
                    $item->expiresAfter(0);

                    return [];
                }
            },
        );
    }
}
