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
    /**
     * @return array<int, array<int, string>>
     */
    public static function gamesReportProvider(): iterable
    {
        return [
            ['49301', '10022', '1035145'],
            ['58766', '95107', '1249171'],
        ];
    }

    public function testJsonSpielplanHasCorrectDataFields(): void
    {
        $spielplan = Helper::getJsonSpielplan('551206');
        $spiel = $spielplan['dataList']['0'];

        $this->assertSame('team', $spielplan['lvTypePathStr']);
        $this->assertSame('551206', $spielplan['lvIDPathStr']);

        // Basisdaten
        $this->assertArrayHasKey('lvTypePathStr', $spielplan);
        $this->assertArrayHasKey('lvIDPathStr', $spielplan);
        $this->assertArrayHasKey('lvTypeLabelStr', $spielplan);
        $this->assertArrayHasKey('dataList', $spielplan);

        // Spielplaneinträge
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
        $this->assertArrayHasKey('gLive', $spiel);
        $this->assertArrayHasKey('gTickerToken', $spiel);
        $this->assertArrayHasKey('gGuestGoals', $spiel);
        $this->assertArrayHasKey('gHomeGoals_1', $spiel);
        $this->assertArrayHasKey('gGuestGoals_1', $spiel);
        $this->assertArrayHasKey('gHomePoints', $spiel);
        $this->assertArrayHasKey('gGuestPoints', $spiel);
        $this->assertArrayHasKey('gComment', $spiel);
        $this->assertArrayHasKey('gReferee', $spiel);
    }

    public function testJsonAllGamesHasCorrectDataFields(): void
    {
        $spielplan = Helper::getJsonLigaSpielplan('49301');
        $spiel = $spielplan['dataList']['0'];

        $this->assertSame('class', $spielplan['lvTypePathStr']);
        $this->assertSame('49301', $spielplan['lvIDPathStr']);

        // Basisdaten
        $this->assertArrayHasKey('lvTypePathStr', $spielplan);
        $this->assertArrayHasKey('lvIDPathStr', $spielplan);
        $this->assertArrayHasKey('lvTypeLabelStr', $spielplan);
        $this->assertArrayHasKey('dataList', $spielplan);

        // Spielplaneinträge
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
        $this->assertArrayHasKey('gLive', $spiel);
        $this->assertArrayHasKey('gTickerToken', $spiel);
        $this->assertArrayHasKey('gGuestGoals', $spiel);
        $this->assertArrayHasKey('gHomeGoals_1', $spiel);
        $this->assertArrayHasKey('gGuestGoals_1', $spiel);
        $this->assertArrayHasKey('gHomePoints', $spiel);
        $this->assertArrayHasKey('gGuestPoints', $spiel);
        $this->assertArrayHasKey('gComment', $spiel);
        $this->assertArrayHasKey('gReferee', $spiel);
    }

    public function testJsonTabelleHasCorrectDataFields(): void
    {
        $tabelle = Helper::getJsonTabelle('69281');
        $team = $tabelle['dataList']['0'];

        $this->assertSame('class', $tabelle['lvTypePathStr']);
        $this->assertSame('69281', $tabelle['lvIDPathStr']);
        // Basisdaten
        $this->assertArrayHasKey('lvTypePathStr', $tabelle);
        $this->assertArrayHasKey('lvIDPathStr', $tabelle);
        $this->assertArrayHasKey('lvTypeLabelStr', $tabelle);
        $this->assertArrayHasKey('dataList', $tabelle);
        $this->assertArrayHasKey('errCode', $tabelle);

        // Tabelleneintrag
        $this->assertArrayHasKey('tabScore', $team);
        $this->assertArrayHasKey('tabTeamname', $team);
        $this->assertArrayHasKey('numPlayedGames', $team);
        $this->assertArrayHasKey('numWonGames', $team);
        $this->assertArrayHasKey('numEqualGames', $team);
        $this->assertArrayHasKey('numLostGames', $team);
        $this->assertArrayHasKey('numGoalsShot', $team);
        $this->assertArrayHasKey('numGoalsGot', $team);
        $this->assertArrayHasKey('pointsPlus', $team);
        $this->assertArrayHasKey('pointsMinus', $team);
    }

    public function testJsonVereinHasCorrectDataFields(): void
    {
        $spielplan = Helper::getJsonVerein('6201');
        // $spiel = $spielplan['dataList']['0'];

        $this->assertSame('club', $spielplan['lvTypePathStr']);
        $this->assertSame('6201', $spielplan['lvIDPathStr']);

        // Basisdaten
        $this->assertArrayHasKey('lvTypePathStr', $spielplan);
        $this->assertArrayHasKey('lvIDPathStr', $spielplan);
        $this->assertArrayHasKey('lvTypeLabelStr', $spielplan);
        $this->assertArrayHasKey('dataList', $spielplan);
        $this->assertArrayHasKey('errCode', $spielplan);

        // Spielplaneinträge; $this->assertArrayHasKey('gID', $spiel);
        // $this->assertArrayHasKey('gNo', $spiel);  $this->assertArrayHasKey('gClassID',
        // $spiel);  $this->assertArrayHasKey('gClassSname', $spiel);
        // $this->assertArrayHasKey('gDate', $spiel);  $this->assertArrayHasKey('gTime',
        // $spiel);  $this->assertArrayHasKey('gGymnasiumID', $spiel);
        // $this->assertArrayHasKey('gGymnasiumNo', $spiel);
        // $this->assertArrayHasKey('gGymnasiumName', $spiel);
        // $this->assertArrayHasKey('gGymnasiumPostal', $spiel);
        // $this->assertArrayHasKey('gGymnasiumTown', $spiel);
        // $this->assertArrayHasKey('gGymnasiumStreet', $spiel);
        // $this->assertArrayHasKey('gHomeTeam', $spiel);
        // $this->assertArrayHasKey('gGuestTeam', $spiel);
        // $this->assertArrayHasKey('gHomeGoals', $spiel);
        // $this->assertArrayHasKey('gLive', $spiel);
        // $this->assertArrayHasKey('gTickerToken', $spiel);
        // $this->assertArrayHasKey('gGuestGoals', $spiel);
        // $this->assertArrayHasKey('gHomeGoals_1', $spiel);
        // $this->assertArrayHasKey('gGuestGoals_1', $spiel);
        // $this->assertArrayHasKey('gHomePoints', $spiel);
        // $this->assertArrayHasKey('gGuestPoints', $spiel);
        // $this->assertArrayHasKey('gComment', $spiel);
        // $this->assertArrayHasKey('gReferee', $spiel);
    }

    /**
     * @dataProvider gamesReportProvider
     *
     * @param string $ligaID
     * @param string $gameNo
     * @param string $sGID
     */
    public function testGetsCorrectReportNo($ligaID, $gameNo, $sGID): void
    {
        $reportNo = Helper::getReportNo($ligaID, $gameNo);
        $this->assertSame($sGID, $reportNo);
    }
}
