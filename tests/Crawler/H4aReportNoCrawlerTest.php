<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

use Janborg\H4aTabellen\Crawler\H4aReportNoCrawler;
use PHPUnit\Framework\TestCase;

class H4aReportNoCrawlerTest extends TestCase
{
    /**
     * @return array<int, array<int, string>>
     */
    public static function gamesProvider(): iterable
    {
        return [
            ['handball4all', 'wuerttemberg', '126171', 'm-bol_hf', '7762911', '2651371'],
            ['handball4all', 'baden', '118076', 'f-olb_bhv', '7448846', '2985786'],
        ];
    }

    /**
     * @dataProvider gamesProvider
     *
     * @param string $provider
     * @param string $verbandName
     * @param string $classID
     * @param string $classShortName
     * @param string $gGameID
     * @param string $sGID
     */
    public function testGetsReportNo($provider, $verbandName, $classID, $classShortName, $gGameID, $sGID): void
    {
        $crawler = new H4aReportNoCrawler();
        $crawler->setProvider($provider);
        $crawler->setVerbandName($verbandName);
        $crawler->setClassID($classID);
        $crawler->setClassShortName($classShortName);
        $crawler->setgGameID($gGameID);

        $crawler->crawlReportNo();
        $reportNo = $crawler->getSGid();

        $this->assertIsString($reportNo);
        $this->assertNotEmpty($reportNo);
        $this->assertSame($sGID, $reportNo);
    }
}
