<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_gamestats.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

namespace Janborg\H4aTabellen\Crawler;

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
     * @var array<mixed>
     */
    private array $teams;

    private Crawler $crawler;

    public function setClubID(string $clubID): void
    {
        $this->clubID = $clubID;
    }

    public function setProvider($provider): void
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
     * @return array<mixed>
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
                $arrTeams[$i]['teamUrl'] = $node->attr('href');

                $arrTeams[$i]['teamName'] = $node->filterXPath('//div[contains(@class, "list-item-title")]')->text();

                $arrTeams[$i]['ligaName'] = $node->filterXPath('//div[contains(@class, "list-item-text")]')->text();                
            },
        );

        // add teamID to array
        foreach ($arrTeams as &$team) {
            $team['teamID'] = $this->extractTeamID($team['teamUrl']);
            $team['provider'] = $this->extractProvider($team['teamUrl']);
            $team['verband'] = $this->extractVerband($team['teamUrl']);

            $team = $this->crawlLigaInfosForTeam($team);
        }


        $this->teams = $arrTeams;
    }

    private function crawlLigaInfosForTeam(array $team): array
    {
        $url = $this->baseUrl.$team['teamUrl'];

        $httpClient = HttpClient::create();

        $response = $httpClient->request('GET', $url);

        $html = $response->getContent();

        $crawler = new Crawler($html);

        $links = [];

        $crawler->filterXPath('//a[contains(@class, "schedule-list-item")]')->each(
            function (Crawler $link) use(&$links) {
                $links[] = $link->attr('href');
            }
        );

        if (isset($links[0])) {
            // add ligaUrl to team
            $ligaUrl = $links[0];
        }
            // add ligaID to team
            $team['classID'] = isset($ligaUrl) ?  $this->extractLigaID($ligaUrl) : '' ;

            // add ligaShortName to team
            $team['classShortName'] = isset($ligaUrl) ? $this->extractLigaShortName($ligaUrl) : '';

        return $team;
    }
   

    private function extractTeamID(string $url): string
    {
        preg_match('/mannschaften\/\w+\.\w+\.([0-9,-]+)\//', $url, $matches);

        return $matches[1] ?? '';
    }

    private function extractProvider(string $url): string
    {
        preg_match('/mannschaften\/(\w+)\.\w+\.[0-9,-]+\//', $url, $matches);

        return $matches[1] ?? '';
    }

    private function extractVerband(string $url): string
    {
        preg_match('/mannschaften\/\w+\.(\w+)\.[0-9,-]+\//', $url, $matches);

        return $matches[1] ?? '';
    }

    private function extractLigaID(string $url): string
    {
        preg_match('/spielplan\/spieltage\/\w+\.\w+\.([w,0-9,-,\.]+)\/spiele\//', $url, $matches);

        return $matches[1] ?? '';
    }

    private function extractLigaShortName(string $url): string
    {
        preg_match('/ligen\/\w+\.\w+\.([a-zA-Z0-9_,-]+)\/spielplan\/spieltage\//', $url, $matches);

        return $matches[1] ?? '';
    }
}
