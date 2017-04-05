<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'HeimrichHannot',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Elements
	'HeimrichHannot\Share\Elements\ContentModule' => 'system/modules/share/elements/ContentModule.php',

	// Classes
	'HeimrichHannot\Share\Hooks'                  => 'system/modules/share/classes/Hooks.php',
	'HeimrichHannot\Share\Share'                  => 'system/modules/share/classes/Share.php',
	'HeimrichHannot\Share\Backend\Module'         => 'system/modules/share/classes/backend/Module.php',
	'HeimrichHannot\Share\PrintPage'              => 'system/modules/share/classes/PrintPage.php',
	'HeimrichHannot\Share\TCPDF_CustomPdf'        => 'system/modules/share/classes/TCPDF_CustomPdf.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'share_print_default' => 'system/modules/share/templates/share/print',
	'share_default'       => 'system/modules/share/templates/share',
));
