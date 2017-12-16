<?php

namespace Janborg\H4aTabellen;
use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
class Plugin implements BundlePluginInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function getBundles(ParserInterface $parser)
	{
		return [
			BundleConfig::create(JanborgH4aTabellenBundle::class)
				->setLoadAfter([ContaoCoreBundle::class])
				->setReplace(['h4a_tabellen']),
		];
	}
}
