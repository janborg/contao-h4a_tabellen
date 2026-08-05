<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

namespace Janborg\H4aTabellen\Tests;

use Janborg\H4aTabellen\JanborgH4aTabellenBundle;
use PHPUnit\Framework\TestCase;

final class JanborgH4aTabellenBundleTest extends TestCase
{
    public function testHasExpectedName(): void
    {
        $bundle = new JanborgH4aTabellenBundle();

        $this->assertSame('JanborgH4aTabellenBundle', $bundle->getName());
    }

    public function testHasExpectedPath(): void
    {
        $bundle = new JanborgH4aTabellenBundle();

        $this->assertDirectoryExists($bundle->getPath());
    }
}
