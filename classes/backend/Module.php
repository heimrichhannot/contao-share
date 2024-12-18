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


use Contao\Backend;
use Contao\Controller;
use Contao\ModuleModel;

class Module extends Backend
{

    /**
     * Get all share print templates
     * @param \DataContainer $dc
     *
     * @return array
     */
    public function getPrintSoloTemplates(\DataContainer $dc)
    {
        return Controller::getTemplateGroup('share_print');
    }

    public function modifyPalette(\DataContainer $objDc)
    {
        $objModule = \ModuleModel::findByPk($objDc->id);
        $arrDca = &$GLOBALS['TL_DCA']['tl_module'];

        if ($objModule !== null && $objModule->addShare)
        {
            $arrButtons = deserialize($objModule->share_buttons, true);

            if (in_array('feedback', $arrButtons))
            {
                $arrDca['subpalettes']['addShare'] = str_replace('share_buttons', 'share_buttons,share_feedbackEmail,share_feedbackSubject', $arrDca['subpalettes']['addShare']);
            }

            if (in_array('mailto', $arrButtons))
            {
                $arrDca['subpalettes']['addShare'] = str_replace('share_buttons', 'share_buttons,share_mailtoSubject', $arrDca['subpalettes']['addShare']);
            }

            if (in_array('pdfButton', $arrButtons))
            {
                $arrDca['subpalettes']['addShare'] = str_replace('share_buttons', 'share_buttons,share_customPrintTpl,share_pdfRenderer', $arrDca['subpalettes']['addShare']);
            }

            if (in_array('printButton', $arrButtons))
            {
                $arrDca['subpalettes']['addShare'] = str_replace('share_customPrintTpl', '', $arrDca['subpalettes']['addShare']);
                $arrDca['subpalettes']['addShare'] = str_replace('share_buttons', 'share_buttons,share_customPrintTpl', $arrDca['subpalettes']['addShare']);
            }
        }
    }

    /**
     * Returns module with share activated
     */
    public static function getModuleWithShare ()
    {
        $modules = ModuleModel::findBy('addShare', '1');
        foreach ($modules as $module)
        {
            $values[$module->id] = $module->name.' ['.$module->type.'] (ID:'.$module->id.')';
        }
        return $values;
    }

}