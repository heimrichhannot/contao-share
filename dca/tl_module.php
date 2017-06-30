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
$dc['subpalettes']['addShare'] = 'share_buttons,share_customPrintTpl,share_pdfRenderer,share_pdfCssSRC,share_pdfLogoSRC,share_pdfLogoSize,share_pdfFontSRC,share_pdfFontSize,share_pdfFooterText';

$arrFields = [
    'addShare'             => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['addShare'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''",
    ],
    'share_buttons'        => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['share_buttons'],
        'exclude'   => true,
        'inputType' => 'checkboxWizard',
        'reference' => $GLOBALS['TL_LANG']['tl_module']['references']['share_buttons'],
        'options'   => ['pdfButton', 'printButton', 'facebook', 'twitter', 'gplus'],
        'eval'      => ['multiple' => true, 'mandatory' => true],
        'sql'       => "blob NULL",
    ],
    'share_customPrintTpl' => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['share_customPrintTpl'],
        'exclude'          => true,
        'inputType'        => 'select',
        'options_callback' => ['HeimrichHannot\Share\Backend\Module', 'getPrintSoloTemplates'],
        'eval'             => ['includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
        'sql'              => "varchar(64) NOT NULL default ''",
    ],
    'share_pdfRenderer' => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['share_pdfRenderer'],
        'exclude'          => true,
        'inputType'        => 'select',
        'options'          => ['tcpdf', 'mpdf'],
        'eval'             => ['includeBlankOption' => false, 'chosen' => true, 'tl_class' => 'w50'],
        'sql'              => "varchar(64) NOT NULL default ''",
    ],
    'share_pdfCssSRC'      => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['share_pdfCssSRC'],
        'exclude'   => true,
        'inputType' => 'fileTree',
        'eval'      => ['filesOnly' => true, 'fieldType' => 'radio', 'tl_class' => 'clr', 'extensions' => 'css'],
        'sql'       => "binary(16) NULL",
    ],
    'share_pdfLogoSRC'     => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['share_pdfLogoSRC'],
        'exclude'   => true,
        'inputType' => 'fileTree',
        'eval'      => ['filesOnly' => true, 'fieldType' => 'radio', 'tl_class' => 'clr'],
        'sql'       => "binary(16) NULL",
    ],
    'share_pdfFontSRC'     => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['share_pdfFontSRC'],
        'exclude'   => true,
        'inputType' => 'fileTree',
        'eval'      => ['filesOnly' => true, 'fieldType' => 'checkbox', 'tl_class' => 'clr', 'extensions' => 'ttf', 'multiple' => true],
        'sql'       => "blob NULL",
    ],
    'share_pdfFontSize'    => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['share_pdfFontSize'],
        'exclude'   => true,
        'inputType' => 'text',
        'default'   => 13,
        'eval'      => ['maxlength' => 10, 'rgxp' => 'digit'],
        'sql'       => "int(10) unsigned NOT NULL default '0'",
    ],
    'share_pdfLogoSize'    => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['share_pdfLogoSize'],
        'exclude'   => true,
        'inputType' => 'text',
        'eval'      => ['multiple' => true, 'size' => 2, 'tl_class' => 'w50 clr', 'rgxp' => 'digit'],
        'sql'       => "varchar(255) NOT NULL default ''",
    ],
    'share_pdfFooterText'  => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['share_pdfFooterText'],
        'exclude'   => true,
        'inputType' => 'textarea',
        'eval'      => ['tl_class' => 'clr'],
        'sql'       => "mediumtext NULL",
    ],

];

$dc['fields'] = array_merge($dc['fields'], $arrFields);