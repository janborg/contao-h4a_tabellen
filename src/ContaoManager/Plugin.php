<?php

namespace Janborg\H4aTabellen\ContaoManager;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Janborg\H4aTabellen\JanborgH4aTabellenBundle;
use Contao\CoreBundle\ContaoCoreBundle;

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
							'Contao\ContaoManager\ContaoManagerBundle',])
		];
	}
}
