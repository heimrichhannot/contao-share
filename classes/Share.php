<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 *
 * @package share
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Share;


use Ausi\SlugGenerator\SlugGenerator;
use Ausi\SlugGenerator\SlugOptions;
use Contao\CalendarEventsModel;
use Contao\Environment;
use Contao\File;
use Contao\Frontend;
use Contao\FrontendTemplate;
use Contao\Input;
use Contao\Model;
use Contao\Model\Collection;
use Contao\Module;
use Contao\ModuleModel;
use Contao\ModuleProxy;
use HeimrichHannot\Haste\Util\Url;
use HeimrichHannot\Request\Request;
use Kigkonsult\Icalcreator\Vcalendar;
use Kigkonsult\Icalcreator\Vevent;

class Share extends Frontend
{
    protected $strItem;

    protected $strTemplate = 'share_default';

    protected $arrData = [];
    protected $objModel = null;
    protected $objModule = null;
    protected $objCurrent = null;

    protected $socialShare = false;

    const SHARE_REQUEST_PARAMETER_PRINT = 'print';
    const SHARE_REQUEST_PARAMETER_PDF   = 'pdf';
    const SHARE_REQUEST_PARAMETER_ICAL  = 'ical';

    public function __construct($objModule, $objCurrent)
    {
        if ($objModule instanceof Model) {
            $this->objModel = $objModule;
        } elseif ($objModule instanceof Collection) {
            $this->objModel = $objModule->current();
        }
        $this->objModule = $objModule;

        parent::__construct();

        $this->objCurrent = $objCurrent;

        $this->arrData = $objModule->row();
        $this->space   = deserialize($objModule->space);
        $this->cssID   = deserialize($objModule->cssID, true);

        $arrHeadline    = deserialize($objModule->headline);
        $this->headline = is_array($arrHeadline) ? $arrHeadline['value'] : $arrHeadline;
        $this->hl       = is_array($arrHeadline) ? $arrHeadline['unit'] : 'h1';

        $this->setDefaultButtons($this->arrData['type']);

        $arrButtons = deserialize($this->arrData['share_buttons'], true);

        // overwrite buttons
        if (!empty($arrButtons)) {
            $this->pdfButton            = false;
            $this->printButton          = false;
            $this->printWithoutTemplate = false;
            $this->mailto               = false;
            $this->feedback             = false;
            $this->facebook             = false;
            $this->twitter              = false;
            $this->whatsapp             = false;
            $this->linkedin             = false;

            foreach ($arrButtons as $key => $strType) {
                $this->{$strType} = true;
            }
        }
    }

    protected function setDefaultButtons($strType)
    {
        switch ($strType) {
            case 'newsreader':
            case 'newsreader_plus':
                $this->pdfButton            = true;
                $this->printButton          = true;
                $this->printWithoutTemplate = false;
                $this->mailto               = true;
                $this->feedback             = true;
                $this->facebook             = true;
                $this->twitter              = true;
                $this->whatsapp             = true;
                $this->linkedin             = true;
                break;
            case 'eventreader':
            case 'eventreader_plus':
                $this->pdfButton            = true;
                $this->printButton          = true;
                $this->printWithoutTemplate = false;
                $this->mailto               = true;
                $this->feedback             = true;
                $this->icalButton           = true;
                $this->facebook             = true;
                $this->twitter              = true;
                $this->whatsapp             = true;
                $this->linkedin             = true;
                break;
            default:
                $this->pdfButton            = true;
                $this->printButton          = true;
                $this->printWithoutTemplate = false;
                $this->mailto               = true;
                $this->feedback             = true;
                $this->icalButton           = false;
                $this->facebook             = true;
                $this->twitter              = true;
                $this->whatsapp             = true;
                $this->linkedin             = true;
        }
    }


    public function generate()
    {
        if (Request::hasGet(static::SHARE_REQUEST_PARAMETER_PDF)) {
            if (Request::getGet(Share::SHARE_REQUEST_PARAMETER_PDF) != $this->objModel->id) {
                return;
            }
        }

        // Export iCal
        if (Request::hasGet(Share::SHARE_REQUEST_PARAMETER_ICAL)) {
            $this->generateIcal(Input::get(Share::SHARE_REQUEST_PARAMETER_ICAL));

            return;
        }

        // PDF
        if (Request::hasGet(static::SHARE_REQUEST_PARAMETER_PDF)) {
            $strClass = Module::findClass($this->objModel->type);
            if (!class_exists($strClass)) {
                return;
            }
            $objModule = new $strClass($this->objModel);
            if (!$objModule->addShare) {
                return;
            }
            Input::setGet(static::SHARE_REQUEST_PARAMETER_PDF, false);
            $this->strItem = $objModule->generate();
            $this->generatePdf();
        }


        // Render share buttons
        $this->Template = new FrontendTemplate($this->strTemplate);

        $this->Template->setData($this->arrData);

        $this->compile();

        // Do not change this order (see #6191)
        $this->Template->style = !empty($this->arrStyle) ? implode(' ', $this->arrStyle) : '';
        $this->Template->class = trim('share share_' . $this->type . ' ' . $this->cssID[1]);
        $this->Template->cssID = ($this->cssID[0] != '') ? ' id="' . $this->cssID[0] . '"' : '';

        return $this->Template->parse();
    }

    public function generateShareUrls()
    {
        $list             = [];
        $list['mailto']   = $this->Template->mailto;
        $list['facebook'] = $this->Template->facebookShareUrl;
        $list['twitter']  = $this->Template->twitterShareUrl;
        $list['linkedin'] = $this->Template->linedinShareUrl;
        return $list;
    }


    protected function compile()
    {
        global $objPage;

        // Add syndication variables
        $request = Url::removeAllParametersFromUri(Environment::get('indexFreeRequest'));

        if (!$this->printWithoutTemplate && $this->share_customPrintTpl) {
            $this->Template->printUrl = Url::addQueryString(static::SHARE_REQUEST_PARAMETER_PRINT . '=' . $this->id);
        } else {
            $this->Template->printUrl = 'javascript:window.print();';
        }

        $this->Template->pdfUrl  = Url::addQueryString(static::SHARE_REQUEST_PARAMETER_PDF . '=' . $this->id);
        $this->Template->icalUrl = Url::addQueryString(static::SHARE_REQUEST_PARAMETER_ICAL . '=' . $this->objCurrent->id);
        $this->Template->icalUrl = Url::addQueryString('title=Termin speichern', $this->Template->icalUrl);

        $this->rawUrl   = Url::getCurrentUrl();
        $this->rawTitle = html_entity_decode($objPage->pageTitle ?: $this->objCurrent->headline ?: $this->objCurrent->title);

        $this->Template->encUrl   = rawurlencode($this->rawUrl);
        $this->Template->encTitle = rawurlencode($this->rawTitle);

        $strSubject                   = sprintf($this->share_mailtoSubject, $this->Template->encTitle) ?: $this->Template->encTitle;
        $this->Template->mailto       = 'mailto:?subject=' . $strSubject . '&body=' . $this->Template->encUrl;
        $this->Template->mailtoButton = $this->mailto;

        $strSubject                     = sprintf($this->share_feedbackSubject, $this->Template->encTitle) ?: $this->Template->encTitle;
        $this->Template->feedback       = 'mailto:' . $this->share_feedbackEmail . '?subject=' . $strSubject . '&body=' . $this->Template->encUrl;
        $this->Template->feedbackButton = $this->mailto;

        $this->Template->facebookShareUrl = $this->generateSocialLink("facebook");
        $this->Template->twitterShareUrl  = $this->generateSocialLink("twitter");
        $this->Template->linkedinShareUrl  = $this->generateSocialLink("linkedin");
        $this->Template->whatsappShareUrl  = $this->generateSocialLink("whatsapp");

        $this->Template->printAttributes =
        $this->Template->printTitle = specialchars($GLOBALS['TL_LANG']['MSC']['printPage']);
        $this->Template->pdfTitle        = specialchars($GLOBALS['TL_LANG']['MSC']['printAsPdf']);
        $this->Template->mailtoTitle     = specialchars($GLOBALS['TL_LANG']['MSC']['mailtoTitle']);
        $this->Template->facebookTitle   = specialchars($GLOBALS['TL_LANG']['MSC']['facebookShare']);
        $this->Template->twitterTitle    = specialchars($GLOBALS['TL_LANG']['MSC']['twitterShare']);
        $this->Template->linkedinTitle   = specialchars($GLOBALS['TL_LANG']['MSC']['linkedinShare']);
        $this->Template->whatsappTitle   = specialchars($GLOBALS['TL_LANG']['MSC']['whatsappShare']);
        $this->Template->icalTitle       = specialchars($GLOBALS['TL_LANG']['MSC']['icalShareTitle']);
        $this->Template->facebookButton  = $this->facebook;
        $this->Template->twitterButton   = $this->twitter;
        $this->Template->linkedinButton  = $this->linkedin;
        $this->Template->whatsappButton  = $this->whatsapp;

        $this->Template->socialShare = $this->twitter || $this->facebook || $this->linkedin || $this->whatsapp;
        $this->Template->shareTitle  = specialchars($GLOBALS['TL_LANG']['MSC']['shareTitle']);
    }


    /**
     * Support share print for modules
     *
     * @param ModuleModel $objRow
     * @param string $strBuffer
     * @param Module|array $objModule
     *
     * @return string The output buffer is always returned
     */
    public static function renderPrintableModule($objModel, $strBuffer, $objModule = null)
    {
        $arrButtons = deserialize($objModel->share_buttons, true);

        if (!in_array('printButton', $arrButtons)) {
            return $strBuffer;
        }

        if (!Request::hasGet(static::SHARE_REQUEST_PARAMETER_PRINT)) {
            return $strBuffer;
        }

        if (Request::getGet(Share::SHARE_REQUEST_PARAMETER_PRINT) != $objModel->id) {
            return $strBuffer;
        }

        global $objPage;

        if ($objModel->share_customPrintTpl == '') {
            return $strBuffer;
        }

        if ($objModule instanceof Module && !($objModule instanceof ModuleProxy)) {
            $config = $objModule->Template->getData();
        } elseif (is_array($objModule)) {
            $config = $objModule;
        } else {
            $config = $objModel->row();
        }

        $objPrintPage = new PrintPage($objModel->share_customPrintTpl, $strBuffer, $config);
        $objPrintPage->generate($objPage);
        exit;
    }

    /**
     * Export an event as iCalendar (ics)
     */
    public function generateIcal($eventID)
    {
        $ical = new Vcalendar();
        $ical->setMethod(Vcalendar::PUBLISH);
        $ical->setXprop("X-WR-TIMEZONE", $GLOBALS['TL_CONFIG']['timeZone']);
        $time = time();

        // Get event
        $objEvent = CalendarEventsModel::findByPk($eventID);

        $vevent = new Vevent();
        if ($objEvent->addTime) {
            $vevent->setDtstart(\DateTime::createFromFormat('U', $objEvent->startTime));
            $vevent->setDtend(\DateTime::createFromFormat('U', $objEvent->endTime));
        } else {
            $vevent->setDtstart(\DateTime::createFromFormat('U', $objEvent->startDate), ['VALUE' => 'DATE']);
            if (!strlen($objEvent->endDate) || $objEvent->endDate == 0) {
                $vevent->setDtend(\DateTime::createFromFormat('U', $objEvent->startDate + 24 * 60 * 60), ['VALUE' => 'DATE']);
            } else {
                $vevent->setDtend(\DateTime::createFromFormat('U', $objEvent->endDate + 24 * 60 * 60), ['VALUE' => 'DATE']);
            }
        }
        $vevent->setSummary(htmlspecialchars($objEvent->title, ENT_QUOTES));
        $vevent->setDescription(strip_tags($objEvent->details ? $objEvent->details : $objEvent->teaser));
        if ($objEvent->recurring) {
            $count     = 0;
            $arrRepeat = deserialize($objEvent->repeatEach);
            $arg       = $arrRepeat['value'];
            $unit      = $arrRepeat['unit'];
            if ($arg == 1) {
                $unit = substr($unit, 0, -1);
            }

            $strtotime = '+ ' . $arg . ' ' . $unit;
            $newstart  = strtotime($strtotime, $objEvent->startTime);
            $newend    = strtotime($strtotime, $objEvent->endTime);
            $freq      = 'YEARLY';
            switch ($arrRepeat['unit']) {
                case 'days':
                    $freq = 'DAILY';
                    break;
                case 'weeks':
                    $freq = 'WEEKLY';
                    break;
                case 'months':
                    $freq = 'MONTHLY';
                    break;
                case 'years':
                    $freq = 'YEARLY';
                    break;
            }
            $rrule = ['FREQ' => $freq];
            if ($objEvent->recurrences > 0) {
                $rrule['count'] = $objEvent->recurrences;
            }
            if ($arg > 1) {
                $rrule['INTERVAL'] = $arg;
            }
            $vevent->setRrule($rrule);
        }

        /*
        * begin module event_recurrences handling
        */
        if ($objEvent->repeatExecptions) {
            $arrSkipDates = deserialize($objEvent->repeatExecptions);
            foreach ($arrSkipDates as $skipDate) {
                $exTStamp = strtotime($skipDate);
                $exdate   = [
                    [
                        date('Y', $exTStamp),
                        date('m', $exTStamp),
                        date('d', $exTStamp),
                        date('H', $objEvent->startTime),
                        date('i', $objEvent->startTime),
                        date('s', $objEvent->startTime),
                    ],
                ];
                $vevent->setExdate($exdate);
            }
        }
        /*
        * end module event_recurrences handling
        */
        $generator = new SlugGenerator((new SlugOptions)->setValidChars('a-zA-Z0-9')->setDelimiter('_')->setLocale('de'));
        $ical->setComponent($vevent);

        $vcalendarString = $ical->vtimezonePopulate()->createCalendar();

        $tmpPath = 'files/tmp/share/'.uniqid($generator->generate($objEvent->title) . '_', true).'.ics';
        File::putContent($tmpPath, $vcalendarString);
        
        $file = new File($tmpPath);
        $file->sendToBrowser($generator->generate($objEvent->title) . '.ics');
        $file->delete();
    }


    /**
     * Print an article as PDF and stream it to the browser
     */
    public function generatePdf()
    {
        Share::renderPDFModule($this->objModel, $this->strItem, $this->objModule, true);
    }


    /**
     * @param Model $objModel
     * @param string $strBuffer
     * @param ModuleModel $objModule
     * @param bool $noGetCheck No additional check, if correct get parameter is set
     */
    public static function renderPDFModule($objModel, $strBuffer, $objModule = null, $noGetCheck = false)
    {
        if (!$noGetCheck && Request::getGet(Share::SHARE_REQUEST_PARAMETER_PDF) != $objModel->id) {
            return;
        }
        Input::setGet(static::SHARE_REQUEST_PARAMETER_PDF, false);
        $strFileName = null;

        if ($objModule instanceof ModulePdfReaderInterface) {
            $strFileName = $objModule->getFileName();
        }

        ob_clean();

        global $objPage;
        $pdfPage = new PDFPage($objModel, $strBuffer);

        if (!empty($strFileName)) {
            $pdfPage->setFileName($strFileName);
        }
        if (isset($objModel->share_pdfUsername)) {
            $pdfPage->setLoginUsername($objModel->share_pdfUsername);
        }
        if (isset($objModel->share_pdfPassword)) {
            $pdfPage->setLoginPassword($objModel->share_pdfPassword);
        }
        $pdfPage->generate($objPage);
    }

    public function generateSocialLink($network = null)
    {
        $link = '';
        if (version_compare(VERSION . '.' . BUILD, '4.0', '>=')) {
            switch ($network) {
                case "facebook":
                    $link = 'https://www.facebook.com/sharer/sharer.php?u=' . $this->rawUrl . '&amp;t=' . $this->rawTitle;
                    break;
                case "twitter":
                    $link = 'https://twitter.com/intent/tweet?url=' . $this->rawUrl . '&amp;text=' . $this->rawTitle;
                    break;
                case "linkedin":
                    $link = 'http://www.linkedin.com/shareArticle?mini=true&amp;url=' . $this->rawUrl . '&amp;title=' . $this->rawTitle;
                    break;
                case "whatsapp":
                    $link = 'https://wa.me/?text=' . $this->rawTitle . '%20' . $this->rawUrl;
                    break;
            }
        } else {
            switch ($network) {
                case "facebook":
                    $link = 'share/?p=facebook&amp;u=' . $this->rawUrl . '&amp;t=' . $this->rawTitle;
                    break;
                case "twitter":
                    $link = 'share/?p=twitter&amp;u=' . $this->rawUrl . '&amp;t=' . $this->rawTitle;
                    break;
                case "linkedin":
                    $link = 'http://www.linkedin.com/shareArticle?mini=true&amp;url=' . $this->rawUrl . '&amp;title=' . $this->rawTitle;
                    break;
                case "whatsapp":
                    $link = 'https://wa.me/?text=' . $this->rawTitle . '%20' . $this->rawUrl;
                    break;
            }
        }

        return $link;
    }

    /**
     * Set an object property
     *
     * @param string
     * @param mixed
     */
    public function __set($strKey, $varValue)
    {
        $this->arrData[$strKey] = $varValue;
    }


    /**
     * Return an object property
     *
     * @param string
     *
     * @return mixed
     */
    public function __get($strKey)
    {
        if (isset($this->arrData[$strKey])) {
            return $this->arrData[$strKey];
        }

        return parent::__get($strKey);
    }


    /**
     * Check whether a property is set
     *
     * @param string
     *
     * @return boolean
     */
    public function __isset($strKey)
    {
        return isset($this->arrData[$strKey]);
    }
}
