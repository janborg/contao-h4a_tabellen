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

class JanborgH4aTabellenBundleTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $bundle = new JanborgH4aTabellenBundle();

        $this->assertInstanceOf('Janborg\H4aTabellen\JanborgH4aTabellenBundle', $bundle);
    }
}
