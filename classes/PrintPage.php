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


use Contao\Config;
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
        if ($GLOBALS['TL_LANG']['MSC']['textDirection'] == 'rtl') {
            $this->Template->isRTL = true;
        }

        // HOOK: modify the page object
        if (isset($GLOBALS['TL_HOOKS']['generatePrintPage']) && is_array($GLOBALS['TL_HOOKS']['generatePrintPage'])) {
            foreach ($GLOBALS['TL_HOOKS']['generatePrintPage'] as $callback) {
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

        return $this->generateOutput(true);
    }

    protected function generateHead($objPage)
    {
        // toggle print dialog
        $strScript = 'setTimeout(window.print, 100);';

        // close dialog if not in debug mode
        if (!Request::hasGet('pDebug')) {
            $strScript .= 'setTimeout(window.close, 100);';
        } else {
            return;
        }

        $this->Template->head = \Template::generateInlineScript($strScript);
    }

    protected function generateOutput($blnCheckRequest)
    {
        // Strip non-printable areas
        while (($intStart = strpos($this->Template->buffer, '<!-- print::stop -->')) !== false) {
            if (($intEnd = strpos($this->Template->buffer, '<!-- print::continue -->', $intStart)) !== false) {
                $intCurrent = $intStart;

                // Handle nested tags
                while (($intNested = strpos($this->Template->buffer, '<!-- print::stop -->', $intCurrent + 20)) !== false && $intNested < $intEnd) {
                    if (($intNewEnd = strpos($this->Template->buffer, '<!-- print::continue -->', $intEnd + 24)) !== false) {
                        $intEnd     = $intNewEnd;
                        $intCurrent = $intNested;
                    } else {
                        break;
                    }
                }

                $this->Template->buffer = substr($this->Template->buffer, 0, $intStart) . substr($this->Template->buffer, $intEnd + 24);
            } else {
                break;
            }
        }


        // clear the buffer
        ob_clean();

        if (version_compare(VERSION, '4.0', '<')) {
            // Print the template to the screen
            $this->Template->output($blnCheckRequest);
        } else {

            $this->Template->keywords = '';
            $arrKeywords              = \StringUtil::trimsplit(',', $GLOBALS['TL_KEYWORDS']);

            // Add the meta keywords
            if (strlen($arrKeywords[0])) {
                $this->Template->keywords = str_replace(["\n", "\r", '"'], [' ', '', ''], implode(', ', array_unique($arrKeywords)));
            }

            // Parse the template
            $buffer = $this->Template->parse();

            // HOOK: add custom output filters
            if (isset($GLOBALS['TL_HOOKS']['outputFrontendTemplate']) && is_array($GLOBALS['TL_HOOKS']['outputFrontendTemplate'])) {
                foreach ($GLOBALS['TL_HOOKS']['outputFrontendTemplate'] as $callback) {
                    $this->import($callback[0]);
                    $buffer = $this->{$callback[0]}->{$callback[1]}($buffer, $this->strTemplate);
                }
            }

            // Replace insert tags
            $buffer = $this->replaceInsertTags($buffer, false); // do not cache, otherwise subrequest for esi tags wont work
            $buffer = $this->replaceDynamicScriptTags($buffer);

            // HOOK: allow to modify the compiled markup (see #4291)
            if (isset($GLOBALS['TL_HOOKS']['modifyFrontendPage']) && is_array($GLOBALS['TL_HOOKS']['modifyFrontendPage'])) {
                foreach ($GLOBALS['TL_HOOKS']['modifyFrontendPage'] as $callback) {
                    $this->import($callback[0]);
                    $buffer = $this->{$callback[0]}->{$callback[1]}($buffer, $this->strTemplate);
                }
            }

            $request = Request::getInstance();

            /**
             * @var $kernel \Contao\ManagerBundle\HttpKernel\ContaoKernel
             */
            $kernel = \System::getContainer()->get('kernel');

            $response = new \Symfony\Component\HttpFoundation\Response($buffer);
            $response->headers->set('Content-Type', $this->strContentType . '; charset=' . Config::get('characterSet'));

            $response->send();
            $kernel->terminate($request, $response);

            exit;
        }
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
        if (isset($this->arrData[$strKey])) {
            if (is_object($this->arrData[$strKey]) && is_callable($this->arrData[$strKey])) {
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
