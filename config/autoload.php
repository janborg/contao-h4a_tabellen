<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @license LGPL-3.0+
 */

/**
 * Register the namespace
 */
ClassLoader::addNamespace('H4aTabellen');

/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'H4aTabellen\ContentH4aTabelle' => 'system/modules/contao-h4a_tabellen/classes/ContentH4aTabelle.php',
	'H4aTabellen\ContentH4aSpiele'  => 'system/modules/contao-h4a_tabellen/classes/ContentH4aSpiele.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'ce_h4a_tabelle' => 'system/modules/h4a_tabellen/templates',
	'ce_h4a_spiele'  => 'system/modules/h4a_tabellen/templates',
));
