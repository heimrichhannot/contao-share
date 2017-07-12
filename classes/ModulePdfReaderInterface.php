<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\Share;


interface ModulePdfReaderInterface
{
    /**
     * Get the file name for the current ready entity
     * @return string The filename without .pdf extension
     */
    public function getFileName();
}