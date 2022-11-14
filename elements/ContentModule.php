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

use Contao\ContentModule as ContaoContentModule;
use Contao\ModuleModel;
use HeimrichHannot\Share\Share;

class ContentModule extends ContaoContentModule
{
    /**
     * Parse the template
     *
     * @return string
     */
    public function generate()
    {
        $objModel = ModuleModel::findByPk($this->module);

        if ($objModel === null)
        {
            return '';
        }

        $strBuffer = parent::generate();

        if (!$objModel->addShare)
        {
            return $strBuffer;
        }

        return Share::renderPrintableModule($objModel, $strBuffer, $objModule);
    }

}