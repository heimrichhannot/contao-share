<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 *
 * @package share
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

$dc = &$GLOBALS['TL_DCA']['tl_module'];

/**
 * Palettes
 */
$dc['palettes']['__selector__'][] = 'addShare';


/**
 * Subpalettes
 */
$dc['subpalettes']['addShare'] =
	'share_buttons,share_pdfCssSRC,share_pdfLogoSRC,share_pdfLogoSize,share_pdfFontSRC,share_pdfFontSize,share_pdfFooterText';

$arrFields = array
(
	'addShare'            => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_module']['addShare'],
		'exclude'   => true,
		'inputType' => 'checkbox',
		'eval'      => array('submitOnChange' => true),
		'sql'       => "char(1) NOT NULL default ''",
	),
	'share_buttons'       => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_module']['share_buttons'],
		'exclude'   => true,
		'inputType' => 'checkboxWizard',
		'reference' => $GLOBALS['TL_LANG']['tl_module']['references']['share_buttons'],
		'options'   => array('pdfButton', 'printButton', 'facebook', 'twitter', 'gplus'),
		'eval'      => array('multiple' => true, 'mandatory' => true),
		'sql'       => "blob NULL",
	),
	'share_pdfCssSRC'     => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_module']['share_pdfCssSRC'],
		'exclude'   => true,
		'inputType' => 'fileTree',
		'eval'      => array('filesOnly' => true, 'fieldType' => 'radio', 'tl_class' => 'clr', 'extensions' => 'css'),
		'sql'       => "binary(16) NULL",
	),
	'share_pdfLogoSRC'    => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_module']['share_pdfLogoSRC'],
		'exclude'   => true,
		'inputType' => 'fileTree',
		'eval'      => array('filesOnly' => true, 'fieldType' => 'radio', 'tl_class' => 'clr'),
		'sql'       => "binary(16) NULL",
	),
	'share_pdfFontSRC'    => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_module']['share_pdfFontSRC'],
		'exclude'   => true,
		'inputType' => 'fileTree',
		'eval'      => array('filesOnly' => true, 'fieldType' => 'checkbox', 'tl_class' => 'clr', 'extensions' => 'ttf', 'multiple' => true),
		'sql'       => "blob NULL",
	),
	'share_pdfFontSize'   => array(
		'label'     => &$GLOBALS['TL_LANG']['tl_module']['share_pdfFontSize'],
		'exclude'   => true,
		'inputType' => 'text',
		'default'   => 13,
		'eval'      => array('maxlength' => 10, 'rgxp' => 'digit'),
		'sql'       => "int(10) unsigned NOT NULL default '0'",
	),
	'share_pdfLogoSize'   => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_module']['share_pdfLogoSize'],
		'exclude'   => true,
		'inputType' => 'text',
		'eval'      => array('multiple' => true, 'size' => 2, 'tl_class' => 'w50 clr', 'rgxp' => 'digit'),
		'sql'       => "varchar(255) NOT NULL default ''",
	),
	'share_pdfFooterText' => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_module']['share_pdfFooterText'],
		'exclude'   => true,
		'inputType' => 'textarea',
		'eval'      => array('tl_class' => 'clr'),
		'sql'       => "mediumtext NULL",
	),

);

$dc['fields'] = array_merge($dc['fields'], $arrFields);