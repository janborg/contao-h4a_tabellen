<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

namespace Janborg\H4aTabellen\Tests\HandballNet\Parser;

use Janborg\H4aTabellen\HandballNet\Parser\HandballnetTeamsParser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class HandballnetTeamsParserTest extends TestCase
{
    #[DataProvider('invalidResponseProvider')]
    public function testReturnsEmptyArrayForInvalidResponse(string|null $json): void
    {
        $this->assertSame([], (new HandballnetTeamsParser())->parseClubTeams($json));
    }

    /**
     * @return iterable<string, array{string|null}>
     */
    public static function invalidResponseProvider(): iterable
    {
        yield 'null (request failed)' => [null];
        yield 'empty string' => [''];
        yield 'invalid json' => ['<html>404</html>'];
        yield 'json without data' => ['{"error":"not found"}'];
        yield 'data is not a list' => ['{"data":"foo"}'];
        yield 'empty data' => ['{"data":[]}'];
    }
}
