<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

namespace Janborg\H4aTabellen\Tests\Crawler;

use Janborg\H4aTabellen\Crawler\TeamsCrawler;
use Janborg\H4aTabellen\HandballNet\HandballNetTeam;
use PHPUnit\Framework\TestCase;

class TeamsCrawlerTest extends TestCase
{
    /**
     * @return array<int, array<int, string>>
     */
    public static function clubProvider(): iterable
    {
        return [
            ['6201', 'wuerttemberg', 'HSG Heilbronn', 'handball4all'],
            ['581', 'baden', 'TV Hemsbach', 'handball4all'],
            ['1967', 'hamburg', 'SG Altona', 'handball4all'],
            ['5056', 'pfalz', 'HSG Trifels', 'handball4all'],
            ['2214', 'rheinhessen', 'TV Nierstein', 'handball4all'],
            ['2601', 'saar', 'HF Köllertal', 'handball4all'],
            ['1491', 'schleswig-holstein', 'Eckernförder MTV', 'handball4all'],
            ['785', 'suedbaden', 'TV Todtnau', 'handball4all'],
            ['4781', 'westfalen', 'TV Olpe', 'handball4all'],

        /*
             ['30283', 'bhv', 'DJK Neumarkt', 'nuliga'], // bayern
            ['201173', 'hvbr', 'SV Fortuna Prenzlau', 'nuliga'], // brandenburg
            ['10443', 'hvberlin', 'Lichtenrader SV', 'nuliga'], // berlin
            ['18134', 'hhv', 'HC VfL Heppenheim', 'nuliga'], // hessen
            ['117', 'hvmv', 'HC Empor Rostock', 'nuliga'], // mecklenburg-vorpommern
            ['681', 'hvn', 'Handballverein Lüneburg', 'nuliga'], // niedersachsen-bremen
            ['077', 'hvr', 'HSG Wittlich', 'nuliga'], // rheinland
            ['490148', 'hvs', 'SHV Oschatz', 'nuliga'], // sachsen
            ['420512', 'thv', 'HSC Erfurt', 'nuliga'], // thueringen
            ['060015', 'hnr', '1.FC Köln', 'nuliga'], // nordrhein
            ['2486', 'dhbdata', 'Handball Sport Verein Hamburg', 'sportradar'],
        */
        ];
    }

    /**
     * @dataProvider clubProvider
     *
     * @param string $clubID
     * @param string $verbandName
     * @param string $clubName
     * @param string $provider
     */
    public function testGetAllTeams($clubID, $verbandName, $clubName, $provider): void
    {
        $crawler = new TeamsCrawler();

        $crawler->setClubID($clubID);

        $crawler->setProvider($provider);

        $crawler->setVerbandName($verbandName);

        $teams = $crawler->getAllTeams();

        $this->assertIsArray($teams);

        $this->assertNotEmpty($teams);
        $this->assertInstanceOf(HandballNetTeam::class, $teams[0]);
    }
}
