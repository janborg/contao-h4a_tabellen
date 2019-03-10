<?php

/*
 * This file is part of contao-h4a_tabellen.
 * (c) Jan Lünborg
 * @license LGPL-3.0-or-later
 */

namespace Janborg\H4aTabellen\Tests;

use Janborg\H4aTabellen\JanborgH4aTabellenBundle;
use PHPUnit\Framework\TestCase;

class JanborgH4aTabellenBundleTest extends TestCase
{
    public function testCanBeInstantiated()
    {
        $bundle = new JanborgH4aTabellenBundle();

        $this->assertInstanceOf('Janborg\H4aTabellen\JanborgH4aTabellenBundle', $bundle);
    }
}
