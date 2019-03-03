<?php

/*
 * This file is part of contao-h4a_tabellen.
 * (c) Jan Lünborg
 * @license LGPL-3.0-or-later
 */

namespace Janborg\H4aTabellen\ContaoManager;

use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Janborg\H4aTabellen\JanborgH4aTabellenBundle;

class Plugin implements BundlePluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(JanborgH4aTabellenBundle::class)
                ->setLoadAfter(['Contao\CoreBundle\ContaoCoreBundle',
                                'Contao\ContaoManager\ContaoManagerBundle',
                                'Contao\CalendarBundle\ContaoCalendarBundle', ]),
        ];
    }
}
