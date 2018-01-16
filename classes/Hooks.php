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

use Contao\FrontendTemplate;
use Contao\Module;
use Contao\ModuleArticle;
use Contao\ModuleModel;
use HeimrichHannot\Request\Request;

class Hooks
{
    /**
     * Support share print for modules
     *
     * @param ModuleModel $objModel
     * @param string $strBuffer
     * @param Module $objModule
     *
     * @return string
     */
    public static function getFrontendModuleHook($objModel, $strBuffer, $objModule)
    {
        if (!$objModel->addShare)
        {
            return $strBuffer;
        } else
        {
            if (Request::hasGet(Share::SHARE_REQUEST_PARAMETER_PRINT))
            {
                return Share::renderPrintableModule($objModel, $strBuffer, $objModule);
            } elseif (Request::hasGet(Share::SHARE_REQUEST_PARAMETER_PDF))
            {
                return Share::renderPDFModule($objModel, $strBuffer, $objModule);
            } else
            {
                return $strBuffer;
            }
        }
    }

    /**
     * @param string $buffer
     * @param ModuleArticle $article
     */
    public function printArticleAsPdf($buffer, $article)
    {
        if (!$article->addShare)
        {
            return;
        }
        if (!$module = ModuleModel::findById($article->shareModule))
        {
            return;
        }
        Share::renderPDFModule($module, $buffer, null, true);
    }

    /**
     * @param FrontendTemplate $template
     * @param array $data
     */
    public function compileArticle($template, $data, $article)
    {
        if (!$article->addShare)
        {
            return;
        }
        if (!$moduleModel = ModuleModel::findById($article->shareModule))
        {
            return;
        }
        if (Request::hasGet(Share::SHARE_REQUEST_PARAMETER_PRINT))
        {
            Share::renderPrintableModule($moduleModel, $template->parse(), $template->getData());
        }
        if (Request::hasGet(Share::SHARE_REQUEST_PARAMETER_PDF))
        {
            Share::renderPDFModule($moduleModel, $template->parse(), null);
        }
        $current           = new \stdClass();
        $current->headline = $article->headline;
        $current->title    = $article->title;
        $share             = new Share($moduleModel, $current);
        $template->share   = $share->generate();
    }
}