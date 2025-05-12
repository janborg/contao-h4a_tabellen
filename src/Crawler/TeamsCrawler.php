<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

namespace Janborg\H4aTabellen\Crawler;

use Janborg\H4aTabellen\HandballNet\HandballNetTeam;
use Janborg\H4aTabellen\HandballNet\Provider;
use Janborg\H4aTabellen\HandballNet\Verband;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

/**
 * A Crawler for handball.net to get all teams for a known clubID.
 */
class TeamsCrawler
{
    private string $baseUrl = 'https://www.handball.net';

    private string $clubID;

    private string $provider;

    private string $season;

    private string $verbandName;

    /**
     * @var array<HandballNetTeam>
     */
    private array $teams;

    private Crawler $crawler;

    public function __construct(private readonly LoggerInterface|null $errorLogger)
    {
    }

    public function setClubID(string $clubID): void
    {
        $this->clubID = $clubID;
    }

    public function setProvider(string $provider): void
    {
        $this->provider = $provider;
    }

    public function setVerbandName(string $verbandName): void
    {
        $this->verbandName = urlencode(strtolower($verbandName));
    }

    public function setSeason(string $season): void
    {
        $this->season = $season;
    }

    /**
     * creates a Crawler and crawls all the teams for a club. Relevant inputs must be
     * set upfront.
     *
     * @return array<HandballNetTeam>
     */
    public function getAllTeams(): array
    {
        $this->getCrawler();

        $this->crawlTeams();

        return $this->teams;
    }

    private function getClubUrl(): string
    {
        $cluburl = $this->baseUrl.'/vereine/'.$this->provider.'.'.$this->verbandName.'.'.$this->clubID;

        if (isset($this->season)) {
            $cluburl .= '?season='.$this->season;
        }

        return $cluburl;
    }

    private function getCrawler(): void
    {
        $url = $this->getClubUrl();

        $httpClient = HttpClient::create();

        $response = $httpClient->request('GET', $url);

        $html = $response->getContent();

        $this->crawler = new Crawler($html);
    }

    private function crawlTeams(): void
    {
        $divMain = $this->crawler->filterXPath('//body//main');

        $arrTeams = [];

        $divMain->filterXPath('//a[contains(@class, "list-item")]')->each(
            static function (Crawler $node, $i) use (&$arrTeams): void {
                $handballnetTeam = new HandballNetTeam();

                $handballnetTeam->team_url = $node->attr('href');

                $handballnetTeam->team_name = $node->filterXPath('//div[contains(@class, "list-item-title")]')->text();

                // replace the team name in the liga name
                $handballnetTeam->liga_name = trim(str_replace($handballnetTeam->team_name, '', $node->filterXPath('//div[contains(@class, "list-item-text")]')->text()));

                $arrTeams[$i] = $handballnetTeam;
            },
        );

        foreach ($arrTeams as $k => &$team) {
            try {
                $team->team_id = $this->extractTeamID($team->team_url);
                $team->provider = $this->extractProvider($team->team_url);
                $team->verband = $this->extractVerband($team->team_url);            
                $team = $this->crawlLigaInfosForTeam($team);
            } catch (\Throwable $th) {
                $this->errorLogger->error(
                    'Teamcrawler failed for '.$team->team_url.' ('.$th->getMessage().')',
                    ['exception' => $th->getMessage()],
                );
                unset($arrTeams[$k]);
                continue;
            }
        }

        $this->teams = $arrTeams;
    }

    private function crawlLigaInfosForTeam(HandballNetTeam $team): HandballNetTeam
    {
        $url = $this->baseUrl.$team->team_url;

        $httpClient = HttpClient::create();

        $response = $httpClient->request('GET', $url);

        if (200 !== $response->getStatusCode()) {
            return $team;
        }

        $html = $response->getContent();

        $crawler = new Crawler($html);

        $links = [];

        $crawler->filterXPath('//a[contains(@class, "schedule-list-item")]')->each(
            static function (Crawler $link) use (&$links): void {
                $links[] = $link->attr('href');
            },
        );

        if (isset($links[0])) {
            // add ligaUrl to team
            $ligaUrl = $links[0];
        }
        // add ligaID to team
        $team->liga_id = isset($ligaUrl) ? $this->extractLigaID($ligaUrl) : '';

        // add ligaShortName to team
        $team->liga_short_name = isset($ligaUrl) ? $this->extractLigaShortName($ligaUrl) : '';

        return $team;
    }

    private function extractTeamID(string $url): string
    {
        preg_match('/mannschaften\/\w+\.\w+\.([0-9,-]+)\//', $url, $matches);

        return $matches[1] ?? '';
    }

    private function extractProvider(string $url): Provider
    {
        preg_match('/mannschaften\/(\w+)\.[\w,-]+\.[0-9,-]+\//', $url, $matches);

        if (isset($matches[1])) {
            return Provider::from($matches[1]);
        }

        throw new \InvalidArgumentException('Provider not found in URL');
    }

    private function extractVerband(string $url): Verband
    {
        preg_match('/mannschaften\/\w+\.([\w,-]+)\.[0-9,-]+\//', $url, $matches);

        if (isset($matches[1])) {
            return Verband::from($matches[1]);
        }

        throw new \InvalidArgumentException('Verband not found in URL');
    }

    private function extractLigaID(string $url): string
    {
        preg_match('/spielplan\/spieltage\/\w+\.\w+\.([0-9]+)(?:\.[a-zA-Z0-9_-]+)?\/spiele\//', $url, $matches);

        return $matches[1] ?? '';
    }

    private function extractLigaShortName(string $url): string
    {
        preg_match('/ligen\/\w+\.\w+\.([a-zA-Z0-9_,-]+)\/spielplan\/spieltage\//', $url, $matches);

        return $matches[1] ?? '';
    }
}
