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

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

class VerbandsCrawler
{
    private string $baseUrl = 'https://www.handball.net';

    /**
     * @var array<mixed>
     */
    private array $verbaende;

    private Crawler $crawler;

    /**
     * creates a Crawler and crawls all the teams for a club. Relevant inputs must be
     * set upfront.
     *
     * @return array<mixed>
     */
    public function getAllVerbaende(): array
    {
        $this->getCrawler();

        $this->crawlVerbaende();

        return $this->verbaende;
    }

    private function getVerbaendeUrl(): string
    {
        return $this->baseUrl.'/verbaende';
    }

    private function getCrawler(): void
    {
        $url = $this->getVerbaendeUrl();

        $httpClient = HttpClient::create();

        $response = $httpClient->request('GET', $url);

        $html = $response->getContent();

        $this->crawler = new Crawler($html);
    }

    private function crawlVerbaende(): void
    {
        $divMain = $this->crawler->filterXPath('//body//main');

        $arrVerbaende = [];

        // filter for all a-tags with class list-item using filterXPath
        $divMain->filterXPath('//a[contains(@class, "list-item")]')->each(
            static function (Crawler $node, $i) use (&$arrVerbaende): void {
                $arrVerbaende[$i]['verbandsUrl'] = $node->attr('href');

                $arrVerbaende[$i]['verbandName'] = $node->filterXPath('//div[contains(@class, "list-item-title")]')->text();
            },
        );

        foreach ($arrVerbaende as &$verband) {
            $verband['verbandShortName'] = $this->extractVerbandShortName($verband['verbandsUrl']);
        }

        $this->verbaende = $arrVerbaende;
    }

    private function extractVerbandShortName(string $url): string
    {
        // find Baden in /verbaende/Baden/ and find Oberliga-Ostsee-Spree in
        // /verbaende/Oberliga-Ostsee-Spree

        preg_match('/verbaende\/([\w-]+)/', $url, $matches);

        return $matches[1] ?? '';
    }
}
