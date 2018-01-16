<?php

$dca = &$GLOBALS['TL_DCA']['tl_article'];

$fields = [
    'addShare'             => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['addShare'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''",
    ],
    'shareModule' => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['addShare'],
        'exclude'   => true,
        'inputType' => 'select',
        'options_callback' => [\HeimrichHannot\Share\Backend\Module::class, 'getModuleWithShare'],
        'sql'       => "int(10) unsigned NOT NULL default '0'",
    ]
];

$dca['fields'] = array_merge($dca['fields'], $fields);