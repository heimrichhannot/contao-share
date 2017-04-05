<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Share\Elements;

use HeimrichHannot\Share\Share;

class ContentModule extends \ContentModule
{
    /**
     * Parse the template
     *
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'FE' && !BE_USER_LOGGED_IN && ($this->invisible || ($this->start != '' && $this->start > time()) || ($this->stop != '' && $this->stop < time())))
        {
            return '';
        }

        $objModel = \ModuleModel::findByPk($this->module);

        if ($objModel === null)
        {
            return '';
        }

        $strClass = \Module::findClass($objModel->type);

        if (!class_exists($strClass))
        {
            return '';
        }

        $objModel->typePrefix = 'ce_';

        /** @var \Module $objModule */
        $objModule = new $strClass($objModel, $this->strColumn);

        // Overwrite spacing and CSS ID
        $objModule->origSpace = $objModule->space;
        $objModule->space = $this->space;
        $objModule->origCssID = $objModule->cssID;
        $objModule->cssID = $this->cssID;

        $strBuffer = $objModule->generate();

        if (!$objModel->addShare)
        {
            return $strBuffer;
        }

        return Share::renderPrintableModule($objModel, $strBuffer, $objModule);
    }

}