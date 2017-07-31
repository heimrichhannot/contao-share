<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Share\Backend;


class Module extends \Backend
{

    /**
     * Get all share print templates
     * @param \DataContainer $dc
     *
     * @return array
     */
    public function getPrintSoloTemplates(\DataContainer $dc)
    {
        return \Controller::getTemplateGroup('share_print');
    }

    public function modifyPalette(\DataContainer $objDc)
    {
        $objModule = \ModuleModel::findByPk($objDc->id);
        $arrDca = &$GLOBALS['TL_DCA']['tl_module'];

        if ($objModule->addShare)
        {
            $arrButtons = deserialize($objModule->share_buttons, true);

            if (in_array('mailto', $arrButtons))
            {
                $arrDca['palettes'][$objModule->type] = str_replace('addShare', 'addShare,share_mailtoSubject', $arrDca['palettes'][$objModule->type]);
            }

            if (in_array('pdfButton', $arrButtons))
            {
                $arrDca['palettes'][$objModule->type] = str_replace('addShare', 'addShare,share_customPrintTpl,share_pdfRenderer', $arrDca['palettes'][$objModule->type]);
            }

            if (in_array('printButton', $arrButtons))
            {
                $arrDca['palettes'][$objModule->type] = str_replace('share_customPrintTpl', '', $arrDca['palettes'][$objModule->type]);
                $arrDca['palettes'][$objModule->type] = str_replace('addShare', 'addShare,share_customPrintTpl', $arrDca['palettes'][$objModule->type]);
            }
        }
    }

}