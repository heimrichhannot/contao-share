<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @package Share
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
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
	// Classes
	'HeimrichHannot\Share\Share' => 'system/modules/share/classes/Share.php',
    'HeimrichHannot\Share\TCPDF_CustomPdf' => 'system/modules/share/classes/TCPDF_CustomPdf.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'share_default' => 'system/modules/share/templates/share',
));
