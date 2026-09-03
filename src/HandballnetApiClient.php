<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

namespace Janborg\H4aTabellen;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HandballnetApiClient
{
    public function __construct(
        private readonly CacheInterface $appCache,
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $contaoLogger,
        private readonly string $baseApiUrl,
        private readonly int $cacheTtl,
    ) {
    }

    /**
     * Get the data from Handball.net.
     */
    public function getData(string $url, bool $cache = true): string|null
    {
        $cacheKey = md5($url);

        if (!$cache) {
            $this->appCache->delete($cacheKey);
        }

        return $this->appCache->get(
            $cacheKey,
            function (ItemInterface $item) use ($url) {
                $item->expiresAfter($this->cacheTtl);

                try {
                    return $this->httpClient->request('GET', $url, [])->getContent();
                } catch (HttpExceptionInterface|TransportExceptionInterface $e) {
                    $this->contaoLogger->error(\sprintf('Unable to fetch Handballnet data from "%s": %s', $url, $e->getMessage()));
                    $item->expiresAfter(0);

                    return null;
                }
            },
        );
    }

    /**
     * Get all data for a Game from handball.net.
     */
    public function getGameCombinedData(string $game_id, bool $cache = true): string|null
    {
        $url = $this->baseApiUrl.'games/'.$game_id.'/combined';

        return $this->getData($url, $cache);
    }

    /**
     * Get Summary for a Game from handball.net.
     */
    public function getGameSummaryData(string $game_id, bool $cache = true): string|null
    {
        $url = $this->baseApiUrl.'games/'.$game_id;

        return $this->getData($url, $cache);
    }

    /**
     * Get Lineup data for a Game from handball.net.
     */
    public function getGameLineupData(string $game_id, bool $cache = true): string|null
    {
        $url = $this->baseApiUrl.'games/'.$game_id.'/lineups';

        return $this->getData($url, $cache);
    }

    /**
     * Get events data for a Game from handball.net.
     */
    public function getGameEventsData(string $game_id, bool $cache = true): string|null
    {
        $url = $this->baseApiUrl.'games/'.$game_id.'/events';

        return $this->getData($url, $cache);
    }

    /**
     * Get data for a Club from handball.net.
     */
    public function getClubData(string $club_id, bool $cache = true): string|null
    {
        $url = $this->baseApiUrl.'clubs/'.$club_id;

        return $this->getData($url, $cache);
    }

    /**
     * Get Teams data for a Club from handball.net.
     */
    public function getClubTeamsData(string $club_id, string $season, bool $cache = true): string|null
    {
        $url = $this->baseApiUrl.'clubs/'.$club_id.'/teams?season='.$season;

        return $this->getData($url, $cache);
    }

    /**
     * Get data for a Team from handball.net.
     */
    public function getTeamData(string $team_id, bool $cache = true): string|null
    {
        $url = $this->baseApiUrl.'teams/'.$team_id;

        return $this->getData($url, $cache);
    }

    /**
     * Get Schedule data for a Team from handball.net.
     */
    public function getTeamScheduleData(string $team_id, bool $cache = true): string|null
    {
        $url = $this->baseApiUrl.'teams/'.$team_id.'/schedule';

        return $this->getData($url, $cache);
    }

    /**
     * Get data for a tournament from handball.net.
     */
    public function getTournamentData(string $tournament_id, bool $cache = true): string|null
    {
        $url = $this->baseApiUrl.'tournaments/'.$tournament_id;

        return $this->getData($url, $cache);
    }

    /**
     * Get Table data for a tournament from handball.net.
     */
    public function getTournamentTableData(string $tournament_id, bool $cache = true): string|null
    {
        $url = $this->baseApiUrl.'tournaments/'.$tournament_id.'/table';

        return $this->getData($url, $cache);
    }

    /**
     * Get data for an arena from handball.net.
     */
    public function getArenaData(string $field_id, bool $cache = true): string|null
    {
        $url = $this->baseApiUrl.'fields/'.$field_id;

        return $this->getData($url, $cache);
    }
}
