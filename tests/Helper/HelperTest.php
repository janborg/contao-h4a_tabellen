<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

namespace Janborg\H4aTabellen\Tests\Helper;

use Janborg\H4aTabellen\Helper\Helper;
use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{
    public function typeProvider()
    {
        return [
            ['class_games', '0', 'class'],
            ['team', '0', 'team'],
            ['club', '0', 'club'],
            ['class_table', '0', 'class'],
        ];
    }

    public function gamesReportProvider()
    {
        return [
            ['49301', '10022', '1035145'],
            ['58766', '95107', '1249171'],
        ];
    }

    /**
     * @dataProvider typeProvider
     *
     * @param mixed $type
     * @param mixed $id
     * @param mixed $typePath
     */
    public function testGetsCorrectUrlByType($type, $id, $typePath): void
    {
        $liga_url = Helper::getURL($type, $id);
        $data = file_get_contents($liga_url);
        $result = json_decode($data, true);

        $this->assertSame($typePath, $result[0]['lvTypePathStr']);
    }

    public function testJsonSpielplanHasCorrectDataFields(): void
    {
        $spielplan = Helper::getJsonSpielplan('551206');
        $spiel = $spielplan['dataList']['0'];

        $this->assertArrayHasKey('lvTypePathStr', $spielplan);
        $this->assertArrayHasKey('lvIDPathStr', $spielplan);
        $this->assertArrayHasKey('lvTypeLabelStr', $spielplan);
        $this->assertArrayHasKey('dataList', $spielplan);

        $this->assertArrayHasKey('gID', $spiel);
        $this->assertArrayHasKey('gNo', $spiel);
        $this->assertArrayHasKey('gClassID', $spiel);
        $this->assertArrayHasKey('gClassSname', $spiel);
        $this->assertArrayHasKey('gDate', $spiel);
        $this->assertArrayHasKey('gTime', $spiel);
        $this->assertArrayHasKey('gGymnasiumID', $spiel);
        $this->assertArrayHasKey('gGymnasiumNo', $spiel);
        $this->assertArrayHasKey('gGymnasiumName', $spiel);
        $this->assertArrayHasKey('gGymnasiumPostal', $spiel);
        $this->assertArrayHasKey('gGymnasiumTown', $spiel);
        $this->assertArrayHasKey('gGymnasiumStreet', $spiel);
        $this->assertArrayHasKey('gHomeTeam', $spiel);
        $this->assertArrayHasKey('gGuestTeam', $spiel);
        $this->assertArrayHasKey('gHomeGoals', $spiel);
        $this->assertArrayHasKey('gGuestGoals', $spiel);
        $this->assertArrayHasKey('gHomeGoals_1', $spiel);
        $this->assertArrayHasKey('gGuestGoals_1', $spiel);
    }

    public function testJsonAllGamesHasCorrectDataFields(): void
    {
        $spielplan = Helper::getJsonLigaSpielplan('49301');
        $spiel = $spielplan['dataList']['0'];

        $this->assertArrayHasKey('lvTypePathStr', $spielplan);
        $this->assertArrayHasKey('lvIDPathStr', $spielplan);
        $this->assertArrayHasKey('lvTypeLabelStr', $spielplan);
        $this->assertArrayHasKey('dataList', $spielplan);

        $this->assertArrayHasKey('gID', $spiel);
        $this->assertArrayHasKey('gNo', $spiel);
        $this->assertArrayHasKey('gClassID', $spiel);
        $this->assertArrayHasKey('gClassSname', $spiel);
        $this->assertArrayHasKey('gDate', $spiel);
        $this->assertArrayHasKey('gTime', $spiel);
        $this->assertArrayHasKey('gGymnasiumID', $spiel);
        $this->assertArrayHasKey('gGymnasiumNo', $spiel);
        $this->assertArrayHasKey('gGymnasiumName', $spiel);
        $this->assertArrayHasKey('gGymnasiumPostal', $spiel);
        $this->assertArrayHasKey('gGymnasiumTown', $spiel);
        $this->assertArrayHasKey('gGymnasiumStreet', $spiel);
        $this->assertArrayHasKey('gHomeTeam', $spiel);
        $this->assertArrayHasKey('gGuestTeam', $spiel);
        $this->assertArrayHasKey('gHomeGoals', $spiel);
        $this->assertArrayHasKey('gGuestGoals', $spiel);
        $this->assertArrayHasKey('gHomeGoals_1', $spiel);
        $this->assertArrayHasKey('gGuestGoals_1', $spiel);
    }

    /**
     * @dataProvider gamesReportProvider
     *
     * @param mixed $ligaID
     * @param mixed $gameNo
     * @param mixed $sGID
     */
    public function testGetsCorrectReportNo($ligaID, $gameNo, $sGID): void
    {
        $reportNo = Helper::getReportNo($ligaID, $gameNo);
        $this->assertSame($sGID, $reportNo);
    }
}
