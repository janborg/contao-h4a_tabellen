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

class H4aReportNoCrawler
{
    private string $baseUrl = 'https://www.handball.net';

    private string $provider;

    private string $verbandName;

    private string $classID;

    private string $classShortName;

    private string $gGameID;

    private string $sGID;

    private Crawler $crawler;

    public function setProvider(string $provider): void
    {
        $this->provider = $provider;
    }

    public function setClassID(string $classID): void
    {
        $this->classID = $classID;
    }

    public function setClassShortName(string $classShortName): void
    {
        $this->classShortName = urlencode($classShortName);
        $this->classShortName = strtolower($this->classShortName);
    }

    public function setgGameID(string $gGameID): void
    {
        $this->gGameID = $gGameID;
    }

    public function setVerbandName(string $verbandName): void
    {
        $this->verbandName = urlencode($verbandName);
    }

    public function getSGid(): string
    {
        return $this->sGID;
    }

    /**
     * Creates a Crawler and crawls the report (sGID) of a game from handball.net.
     * Relevant inputs must be set upfront.
     */
    public function crawlReportNo(): void
    {
        $this->getCrawler();

        $this->crawlReport();
    }

    private function getCrawler(): void
    {
        $url = $this->getGameUrl();

        $httpClient = HttpClient::create();

        $response = $httpClient->request('GET', $url);

        $html = $response->getContent();

        $this->crawler = new Crawler($html);
    }

    private function getGameUrl(): string
    {
        return $this->baseUrl.'/ligen/'.$this->provider.'.'.$this->verbandName.'.'.$this->classShortName.'/spielplan/spieltage/'.$this->provider.'.'.$this->verbandName.'.'.$this->classID.'/spiele/'.$this->provider.'.'.$this->verbandName.'.'.$this->gGameID;
    }

    private function crawlReport(): void
    {
        // filter link with href containing sGID
        try {
            $reportUrl = $this->crawler->filterXPath('//a[contains(@href, "sGID")]')->attr('href');
        } catch (\InvalidArgumentException $e) {
            $this->sGID = '';
            return;
        }

        $parts = parse_url($reportUrl);

        parse_str($parts['query'], $query);

        if (isset($query['sGID'])) {
            $this->sGID = $query['sGID'];
        }
    }
}
