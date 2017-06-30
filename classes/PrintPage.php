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


use HeimrichHannot\Request\Request;

class PrintPage extends \PageRegular
{
    /**
     * Current Template file
     *
     * @var
     */
    protected $strTemplate;

    /**
     * Current buffer
     *
     * @var
     */
    protected $strBuffer;

    /**
     * Config array
     *
     * @var array
     */
    protected $arrConfig = [];

    /**
     * Template data
     *
     * @var array
     */
    protected $arrData = [];

    public function __construct($strTemplate, $strBuffer, array $arrConfig = [])
    {
        parent::__construct();

        $this->strTemplate = $strTemplate;
        $this->strBuffer   = $strBuffer;
        $this->arrConfig   = $arrConfig;
    }

    /**
     * Generate a print page
     *
     * @param \PageModel $objPage
     * @param boolean    $blnCheckRequest
     */
    public function generate($objPage, $blnCheckRequest = false)
    {
        $GLOBALS['TL_KEYWORDS'] = '';
        $GLOBALS['TL_LANGUAGE'] = $objPage->language;

        \System::loadLanguageFile('default');

        // Static URLs
        $this->setStaticUrls();

        // Get the page layout
        $objLayout = $this->getPageLayout($objPage);

        /**
         * @var \FrontendTemplate
         */
        $this->Template = new \FrontendTemplate($this->strTemplate);

        // Default settings
        $this->Template->layout   = $objLayout;
        $this->Template->language = $GLOBALS['TL_LANGUAGE'];
        $this->Template->charset  = \Config::get('characterSet');
        $this->Template->base     = \Environment::get('base');
        $this->Template->isRTL    = false;

        // Mark RTL languages (see #7171)
        if ($GLOBALS['TL_LANG']['MSC']['textDirection'] == 'rtl')
        {
            $this->Template->isRTL = true;
        }

        // HOOK: modify the page object
        if (isset($GLOBALS['TL_HOOKS']['generatePrintPage']) && is_array($GLOBALS['TL_HOOKS']['generatePrintPage']))
        {
            foreach ($GLOBALS['TL_HOOKS']['generatePrintPage'] as $callback)
            {
                $this->import($callback[0]);
                $this->{$callback[0]}->{$callback[1]}($this->strBuffer, $this->arrConfig, $this);
            }
        }

        // Set the page title and description AFTER the modules have been generated
        $this->Template->mainTitle = $objPage->rootPageTitle;
        $this->Template->pageTitle = $objPage->pageTitle ?: $objPage->title;

        // Meta robots tag
        $this->Template->robots = 'noindex,nofollow';

        // Remove shy-entities (see #2709)
        $this->Template->mainTitle = str_replace('[-]', '', $this->Template->mainTitle);
        $this->Template->pageTitle = str_replace('[-]', '', $this->Template->pageTitle);

        // Fall back to the default title tag
        $strTitleTag = '{{page::pageTitle}} - {{page::rootPageTitle}}';

        // Assign the title and description
        $this->Template->title       = strip_insert_tags($this->replaceInsertTags($strTitleTag));
        $this->Template->description = str_replace(["\n", "\r", '"'], [' ', '', ''], $objPage->description);

        // Execute AFTER the modules have been generated and create footer scripts first
        $this->createFooterScripts($objLayout);
        $this->createHeaderScripts($objPage, $objLayout);

        $this->Template->head = str_replace('[[TL_HEAD]]', '', $this->Template->head);

        $this->Template->buffer = $this->getBuffer();

        $this->generateHead($objPage);

        $this->Template->head = \Template::generateInlineScript($strScript, $objPage->outputFormat != 'html5');

        return $this->generateOutput($blnCheckRequest);
    }

    protected function generateHead($objPage)
    {
        // toggle print dialog
        $strScript = 'window.print();';

        // close dialog if not in debug mode
        if (!Request::hasGet('pDebug'))
        {
            $strScript .= 'setTimeout(window.close, 0);';
        }

        $this->Template->head = \Template::generateInlineScript($strScript, $objPage->outputFormat != 'html5');
    }

    protected function generateOutput ($blnCheckRequest)
    {
        // Print the template to the screen
        $this->Template->output($blnCheckRequest);
    }

    /**
     * Check whether a property is set
     *
     * @param string $strKey The property name
     *
     * @return boolean True if the property is set
     */
    public function __isset($strKey)
    {
        return isset($this->arrData[$strKey]);
    }


    /**
     * Set the template data from an array
     *
     * @param array $arrData The data array
     */
    public function setData($arrData)
    {
        $this->arrData = $arrData;
    }


    /**
     * Return the template data as array
     *
     * @return array The data array
     */
    public function getData()
    {
        return $this->arrData;
    }


    /**
     * Set an object property
     *
     * @param string $strKey   The property name
     * @param mixed  $varValue The property value
     */
    public function __set($strKey, $varValue)
    {
        $this->arrData[$strKey] = $varValue;
    }


    /**
     * Return an object property
     *
     * @param string $strKey The property name
     *
     * @return mixed The property value
     */
    public function __get($strKey)
    {
        if (isset($this->arrData[$strKey]))
        {
            if (is_object($this->arrData[$strKey]) && is_callable($this->arrData[$strKey]))
            {
                return $this->arrData[$strKey]();
            }

            return $this->arrData[$strKey];
        }

        return parent::__get($strKey);
    }

    /**
     * @return mixed
     */
    public function getTemplateFile()
    {
        return $this->strTemplate;
    }

    /**
     * @param mixed $strTemplate
     */
    public function setTemplateFile($strTemplate)
    {
        $this->strTemplate = $strTemplate;
    }

    /**
     * @return mixed
     */
    public function getBuffer()
    {
        return $this->strBuffer;
    }

    /**
     * @param mixed $strBuffer
     */
    public function setBuffer($strBuffer)
    {
        $this->strBuffer = $strBuffer;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->arrConfig;
    }

    /**
     * @param array $arrConfig
     */
    public function setConfig($arrConfig)
    {
        $this->arrConfig = $arrConfig;
    }
}