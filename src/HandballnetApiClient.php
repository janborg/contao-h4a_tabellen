<?php

declare(strict_types=1);

namespace Janborg\H4aTabellen;

use Contao\CoreBundle\Framework\ContaoFramework;
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
        private readonly ContaoFramework $contaoFramework,
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $contaoLogger,
        private readonly string $baseApiUrl,
        private readonly string $projectDir,
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
                } catch (TransportExceptionInterface|HttpExceptionInterface $e) {
                    $this->contaoLogger->error(\sprintf('Unable to fetch Handballnet data from "%s": %s', $url, $e->getMessage()));
                    $item->expiresAfter(0);

                    return null;
                }
            }
        );
    }

    /**
     * Get all data for a Game from handball.net.
     */
    public function getGameCombinedData($id, $cache): string|null
    {
        $url = $this->baseApiUrl.'games/'.$id.'/combined';

        return $this->getData($url, $cache);
    }

    /**
     * Get Summary for a Game from handball.net.
     */
    public function getGameSummaryData($id, $cache): string|null
    {
        $url = $this->baseApiUrl.'games/'.$id;

        return $this->getData($url, $cache);
    }

    /**
     * Get Lineup data for a Game from handball.net.
     */
    public function getGameLineupData($id, $cache): string|null
    {
        $url = $this->baseApiUrl.'games/'.$id.'/lineups';

        return $this->getData($url, $cache);
    }

    /**
     * Get events data for a Game from handball.net.
     */
    public function getGameEventsData($id, $cache): string|null
    {
        $url = $this->baseApiUrl.'games/'.$id.'/events';

        return $this->getData($url, $cache);
    }

    /**
     * Get data for a Club from handball.net.
     */
    public function getClubData($id, $cache): string|null
    {
        $url = $this->baseApiUrl.'clubs/'.$id;

        return $this->getData($url, $cache);
    }

    /**
     * Get Teams data for a Club from handball.net.
     */
    public function getClubTeamsData($id, $season, $cache): string|null
    {
        $url = $this->baseApiUrl.'clubs/'.$id.'/teams?season='.$season;

        return $this->getData($url, $cache);
    }

    /**
     * Get data for a Team from handball.net.
     */
    public function getTeamData($id, $cache): string|null
    {
        $url = $this->baseApiUrl.'teams/'.$id;

        return $this->getData($url, $cache);
    }

    /**
     * Get Schedule data for a Team from handball.net.
     */
    public function getTeamScheduleData($id, $cache): string|null
    {
        $url = $this->baseApiUrl.'teams/'.$id.'/schedule';

        return $this->getData($url, $cache);
    }

    /**
     * Get data for a tournament from handball.net.
     */
    public function getTournamentData($id, $cache): string|null
    {
        $url = $this->baseApiUrl.'tournaments/'.$id;

        return $this->getData($url, $cache);
    }

    /**
     * Get Table data for a tournament from handball.net.
     */
    public function getTournamentTableData($id, $cache): string|null
    {
        $url = $this->baseApiUrl.'tournaments/'.$id.'/table';

        return $this->getData($url, $cache);
    }

    /**
     * Get data for an arena from handball.net.
     */
    public function getArenaData($id, $cache): string|null
    {
        $url = $this->baseApiUrl.'fields/'.$id;

        return $this->getData($url, $cache);
    }
}
