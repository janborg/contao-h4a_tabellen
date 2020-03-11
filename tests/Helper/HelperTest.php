<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 * (c) Jan Lünborg
 * @license LGPL-3.0-or-later
 */

namespace Janborg\H4aTabellen\Tests\ContaoManager;

use Janborg\H4aTabellen\Helper\Helper;
use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{
    public function typeProvider()
    {
        return [
            ['liga', '0', 'class'],
            ['team', '0', 'team'],
            ['verein', '0', 'club'],
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
}
