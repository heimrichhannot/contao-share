<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['getFrontendModule']['share'] = ['HeimrichHannot\Share\Hooks', 'getFrontendModuleHook'];

/**
 * Content elements
 */
$GLOBALS['TL_CTE']['includes']['module'] = 'HeimrichHannot\Share\Elements\ContentModule';