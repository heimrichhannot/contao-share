<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Share;

class Hooks
{
    /**
     * Support share print for modules
     *
     * @param \ModuleModel $objModel
     * @param string       $strBuffer
     * @param \Module      $objModule
     *
     * @return string | Outputbuffer
     */
    public static function getFrontendModuleHook(\ModuleModel $objModel, $strBuffer, \Module $objModule)
    {
        if (!$objModel->addShare)
        {
            return $strBuffer;
        }
        return Share::renderPrintableModule($objModel, $strBuffer, $objModule);
    }

}