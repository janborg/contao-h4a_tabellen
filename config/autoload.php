<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'Contao\ContentH4aTabelle' => 'system/modules/h4a_tabellen/classes/ContentH4aTabelle.php',
	'Contao\ContentH4aSpiele'  => 'system/modules/h4a_tabellen/classes/ContentH4aSpiele.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'ce_h4a_tabelle' => 'system/modules/h4a_tabellen/templates',
	'ce_h4a_spiele'  => 'system/modules/h4a_tabellen/templates',
));
