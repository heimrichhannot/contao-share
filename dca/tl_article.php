<?php

$dca = &$GLOBALS['TL_DCA']['tl_article'];

$dca['palettes']['default'] = str_replace(
    'printable;',
    'printable;{share_legend},addShare;',
    $dca['palettes']['default']
);

$dca['palettes']['__selector__'][]    = 'addShare';
$dca['subpalettes']['addShare'] = 'shareModule';

$fields = [
    'addShare'    => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['addShare'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''",
    ],
    'shareModule' => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['shareModule'],
        'exclude'          => true,
        'inputType'        => 'select',
        'options_callback' => ['HeimrichHannot\Share\Backend\Module', 'getModuleWithShare'],
        'sql'              => "int(10) unsigned NOT NULL default '0'",
    ]
];

$dca['fields'] = array_merge($dca['fields'], $fields);