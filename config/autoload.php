<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2013 Leo Feyer
 *
 * @package   btv_medenlist
 * @author    Volker Riecken
 * @license   LGPL
 * @copyright Riecken Software 2014
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'btv_medenlist',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'btv_medenlist\BTVTeam' => 'system/modules/btv_medenlist/classes/BTVTeam.php',
	'btv_medenlist\BTVClub' => 'system/modules/btv_medenlist/classes/BTVClub.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'btv_team' => 'system/modules/btv_medenlist/templates',
	'btv_club' => 'system/modules/btv_medenlist/templates',
));
