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

$arrDca = &$GLOBALS['TL_DCA']['tl_module'];

/**
 * Callbacks
 */
$arrDca['config']['onload_callback'][] = ['HeimrichHannot\Share\Backend\Module', 'modifyPalette'];

/**
 * Palettes
 */
$arrDca['palettes']['__selector__'][] = 'addShare';
$arrDca['palettes']['__selector__'][] = 'share_pdfRenderer';

/**
 * Subpalettes
 */
$arrDca['subpalettes']['addShare'] = 'share_buttons';

$arrDca['subpalettes']['share_pdfRenderer_tcpdf'] = 'share_pdfShowInline,share_pdfCssSRC,share_pdfLogoSRC,share_pdfLogoSize,share_pdfFontSRC,share_pdfFontSize,share_pdfFooterText';
$arrDca['subpalettes']['share_pdfRenderer_mpdf'] = 'share_pdfShowInline,share_pdfLogoSRC,share_pdfLogoSize,share_pdfFontSize';
$arrDca['subpalettes']['share_pdfRenderer_wkhtmltopdf'] = 'share_pdfShowInline,share_pdfUsername,share_pdfPassword';

/**
 * Fields
 */

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
        'options'   => ['pdfButton', 'printButton', 'printWithoutTemplate', 'mailto', 'feedback', 'facebook', 'twitter', 'gplus'],
        'eval'      => ['multiple' => true, 'mandatory' => true, 'submitOnChange' => true, 'chosen' => true],
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
        'options'          => ['tcpdf', 'mpdf','wkhtmltopdf'],
        'eval'             => ['includeBlankOption' => false, 'chosen' => true, 'tl_class' => 'w50 clr', 'submitOnChange' => true],
        'sql'              => "varchar(64) NOT NULL default ''",
    ],
    'share_pdfShowInline' => [
        'label'                   => &$GLOBALS['TL_LANG']['tl_module']['share_pdfShowInline'],
        'exclude'                 => true,
        'inputType'               => 'checkbox',
        'default'                 => '1',
        'eval'                    => ['chosen' => true, 'tl_class' => 'w50'],
        'sql'                     => "char(1) NOT NULL default '1'"
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
        'eval'      => ['filesOnly' => true, 'fieldType' => 'radio', 'tl_class' => 'clr w50'],
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
        'eval'      => ['maxlength' => 10, 'rgxp' => 'digit', 'tl_class' => 'w50'],
        'sql'       => "int(10) unsigned NOT NULL default '0'",
    ],
    'share_pdfLogoSize'    => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['share_pdfLogoSize'],
        'exclude'   => true,
        'inputType' => 'text',
        'eval'      => ['multiple' => true, 'size' => 2, 'tl_class' => 'w50', 'rgxp' => 'digit'],
        'sql'       => "varchar(255) NOT NULL default ''",
    ],
    'share_pdfFooterText'  => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['share_pdfFooterText'],
        'exclude'   => true,
        'inputType' => 'textarea',
        'eval'      => ['tl_class' => 'clr'],
        'sql'       => "mediumtext NULL",
    ],
    'share_pdfUsername'    => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['share_pdfUsername'],
        'exclude'   => true,
        'inputType' => 'text',
        'default'   => 13,
        'eval'      => ['tl_class' => 'w50 clr'],
        'sql'       => "varchar(50) NOT NULL default ''",
    ],
    'share_pdfPassword'    => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['share_pdfPassword'],
        'exclude'   => true,
        'inputType' => 'text',
        'default'   => 13,
        'eval'      => ['tl_class' => 'w50', 'maxlength' => '50'],
        'sql'       => "varchar(50) NOT NULL default ''",
    ],
    'share_mailtoSubject' => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['share_mailtoSubject'],
        'exclude'          => true,
        'inputType'        => 'text',
        'eval'             => ['tl_class' => 'w50 clr'],
        'sql'              => "varchar(128) NOT NULL default ''",
    ],
    'share_feedbackEmail' => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['share_feedbackEmail'],
        'exclude'          => true,
        'inputType'        => 'text',
        'eval'             => ['tl_class' => 'w50 clr', 'rgxp' => 'email'],
        'sql'              => "varchar(128) NOT NULL default ''",
    ],
    'share_feedbackSubject' => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['share_feedbackSubject'],
        'exclude'          => true,
        'inputType'        => 'text',
        'eval'             => ['tl_class' => 'w50 clr'],
        'sql'              => "varchar(128) NOT NULL default ''",
    ],
];

$arrDca['fields'] = array_merge($arrDca['fields'], $arrFields);