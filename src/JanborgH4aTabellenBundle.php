<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

namespace Janborg\H4aTabellen;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class JanborgH4aTabellenBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
