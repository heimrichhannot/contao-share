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


use HeimrichHannot\Haste\Util\Url;
use HeimrichHannot\Request\Request;
use HeimrichHannot\Share\TCPDF_CustomPdf;

class Share extends \Frontend
{
    protected $strItem;

    protected $strTemplate = 'share_default';

    protected $arrData    = [];
    protected $objModel   = null;
    protected $objCurrent = null;

    protected $socialShare = false;

    const SHARE_REQUEST_PARAMETER_PRINT = 'print';
    const SHARE_REQUEST_PARAMETER_PDF = 'pdf';
    const SHARE_REQUEST_PARAMETER_ICAL = 'ical';

    public function __construct($objModule, $objCurrent)
    {
        if ($objModule instanceof \Model)
        {
            $this->objModel = $objModule;
        }
        elseif ($objModule instanceof \Model\Collection)
        {
            $this->objModel = $objModule->current();
        }

        parent::__construct();

        $this->objCurrent = $objCurrent;

        $this->arrData = $objModule->row();
        $this->space   = deserialize($objModule->space);
        $this->cssID   = deserialize($objModule->cssID, true);

        if ($this->customTpl != '' && TL_MODE == 'FE')
        {
            $this->strTemplate = $this->customTpl;
        }

        $arrHeadline    = deserialize($objModule->headline);
        $this->headline = is_array($arrHeadline) ? $arrHeadline['value'] : $arrHeadline;
        $this->hl       = is_array($arrHeadline) ? $arrHeadline['unit'] : 'h1';

        $this->setDefaultButtons($this->arrData['type']);

        $arrButtons = deserialize($this->arrData['share_buttons'], true);

        // overwrite buttons
        if (!empty($arrButtons))
        {
            $this->pdfButton   = false;
            $this->printButton = false;
            $this->facebook    = false;
            $this->twitter     = false;
            $this->gplus       = false;

            foreach ($arrButtons as $key => $strType)
            {
                $this->{$strType} = true;
            }
        }
    }

    protected function setDefaultButtons($strType)
    {
        switch ($strType)
        {
            case 'newsreader':
            case 'newsreader_plus':
                $this->pdfButton   = true;
                $this->printButton = true;
                $this->facebook    = true;
                $this->twitter     = true;
                $this->gplus       = true;
                break;
            case 'eventreader':
            case 'eventreader_plus':
                $this->pdfButton   = true;
                $this->printButton = true;
                $this->icalButton  = true;
                $this->facebook    = true;
                $this->twitter     = true;
                $this->gplus       = true;
                break;
            default:
                $this->pdfButton   = true;
                $this->printButton = true;
                $this->icalButton  = false;
                $this->facebook    = true;
                $this->twitter     = true;
                $this->gplus       = true;
        }
    }


    public function generate()
    {
        // TODO ???  Print the article as PDF
        /*  if (isset($_GET['pdf'])  && \Input::get('pdf') == $this->objModel->id)
          {
              // Backwards compatibility
              if ($this->objModel->printable == 1)
              {
                  $objArticle = new \ModuleArticle($objRow);
                  $objArticle->generatePdf();
              }
              elseif ($this->objModel->printable != '')
              {
                  $options = deserialize($objRow->printable);

                  if (is_array($options) && in_array('pdf', $options))
                  {
                      $objArticle = new \ModuleArticle($this->objModel);
                      $objArticle->generatePdf();
                  }
              }
          } //*/


        // Export iCal
        if (strlen(\Input::get(Share::SHARE_REQUEST_PARAMETER_ICAL)))
        {
            $this->generateIcal(\Input::get(Share::SHARE_REQUEST_PARAMETER_ICAL));

            return;
        }

        // PDF
        if (strlen(\Input::get(Share::SHARE_REQUEST_PARAMETER_PDF)))
        {

            $strClass = \Module::findClass($this->objModel->type);

            if (!class_exists($strClass))
            {
                return;
            }

            $objModule = new $strClass($this->objModel);

            if (!$objModule->addShare)
            {
                return;
            }

            \Input::setGet("pdf", false);  // prevent endless loops, because of generate

            $this->strItem = $objModule->generate();
            $this->generatePdf();
        }


        // Render share buttons
        $this->Template = new \FrontendTemplate($this->strTemplate);

        $this->Template->setData($this->arrData);

        $this->compile();

        // Do not change this order (see #6191)
        $this->Template->style = !empty($this->arrStyle) ? implode(' ', $this->arrStyle) : '';
        $this->Template->class = trim('share share_' . $this->type . ' ' . $this->cssID[1]);
        $this->Template->cssID = ($this->cssID[0] != '') ? ' id="' . $this->cssID[0] . '"' : '';


        return $this->Template->parse();
    }


    protected function compile()
    {
        global $objPage;

        // Add syndication variables
        $request = Url::removeAllParametersFromUri(\Environment::get('indexFreeRequest'));

        $this->Template->print    = Url::addQueryString(static::SHARE_REQUEST_PARAMETER_PRINT . '=' . $this->id);
        $this->Template->encUrl   = Url::removeAllParametersFromUri(rawurlencode(\Environment::get('base') . \Environment::get('request')));
        $this->Template->encTitle = rawurlencode($objPage->pageTitle);
        $this->Template->href     = $request . ((strpos($request, '?') !== false) ? '&amp;' : '?') . 'pdf=' . $this->id;
        $this->Template->ical     = $request . "?ical=" . $this->objCurrent->id . "&title=" . urlencode("Termin speichern");

        $this->Template->printTitle     = specialchars($GLOBALS['TL_LANG']['MSC']['printPage']);
        $this->Template->pdfTitle       = specialchars($GLOBALS['TL_LANG']['MSC']['printAsPdf']);
        $this->Template->facebookTitle  = specialchars($GLOBALS['TL_LANG']['MSC']['facebookShare']);
        $this->Template->twitterTitle   = specialchars($GLOBALS['TL_LANG']['MSC']['twitterShare']);
        $this->Template->gplusTitle     = specialchars($GLOBALS['TL_LANG']['MSC']['gplusShare']);
        $this->Template->icalTitle      = specialchars($GLOBALS['TL_LANG']['MSC']['icalShareTitle']);
        $this->Template->facebookButton = $this->facebook;
        $this->Template->gplusButton    = $this->gplus;
        $this->Template->twitterButton  = $this->twitter;

        $this->Template->socialShare = $this->twitter || $this->facebook || $this->gplus;
        $this->Template->shareTitle  = specialchars($GLOBALS['TL_LANG']['MSC']['shareTitle']);
    }


    /**
     * Support share print for modules
     *
     * @param \ModuleModel $objRow
     * @param string       $strBuffer
     * @param \Module      $objModule
     *
     * @return string The output buffer is always returned
     */
    public static function renderPrintableModule(\ModuleModel $objModel, $strBuffer, \Module $objModule)
    {
        $arrButtons = deserialize($objModel->share_buttons, true);

        if (!in_array('printButton', $arrButtons))
        {
            return $strBuffer;
        }

        if (!Request::hasGet(static::SHARE_REQUEST_PARAMETER_PRINT))
        {
            return $strBuffer;
        }

        if (Request::getGet(Share::SHARE_REQUEST_PARAMETER_PRINT) != $objModel->id)
        {
            return $strBuffer;
        }

        global $objPage;

        if ($objModel->share_customPrintTpl == '')
        {
            return $strBuffer;
        }

        $objPrintPage = new PrintPage($objModel->share_customPrintTpl, $strBuffer, $objModule->Template->getData());
        $objPrintPage->generate($objPage);
        exit;
    }

    /**
     * Export an event as iCalendar (ics)
     */
    public function generateIcal($eventID)
    {
        $ical = new \vcalendar();
        $ical->setConfig('ical_' . $this->id);
        $ical->setProperty('method', 'PUBLISH');
        $ical->setProperty("X-WR-TIMEZONE", $GLOBALS['TL_CONFIG']['timeZone']);
        $time = time();

        // Get event
        $objEvent = \CalendarEventsModel::findByPk($eventID);

        $vevent = new \vevent();
        if ($objEvent->addTime)
        {
            $vevent->setProperty(
                'dtstart',
                [
                    'year'  => date('Y', $objEvent->startTime),
                    'month' => date('m', $objEvent->startTime),
                    'day'   => date('d', $objEvent->startTime),
                    'hour'  => date('H', $objEvent->startTime),
                    'min'   => date('i', $objEvent->startTime),
                    'sec'   => 0,
                ]
            );
            $vevent->setProperty(
                'dtend',
                [
                    'year'  => date('Y', $objEvent->endTime),
                    'month' => date('m', $objEvent->endTime),
                    'day'   => date('d', $objEvent->endTime),
                    'hour'  => date('H', $objEvent->endTime),
                    'min'   => date('i', $objEvent->endTime),
                    'sec'   => 0,
                ]
            );
        }
        else
        {
            $vevent->setProperty('dtstart', date('Ymd', $objEvent->startDate), ['VALUE' => 'DATE']);
            if (!strlen($objEvent->endDate) || $objEvent->endDate == 0)
            {
                $vevent->setProperty('dtend', date('Ymd', $objEvent->startDate + 24 * 60 * 60), ['VALUE' => 'DATE']);
            }
            else
            {
                $vevent->setProperty('dtend', date('Ymd', $objEvent->endDate + 24 * 60 * 60), ['VALUE' => 'DATE']);
            }
        }
        $vevent->setProperty('summary', $objEvent->title, ENT_QUOTES, 'UTF-8');
        $vevent->setProperty('description', strip_tags($objEvent->details ? $objEvent->details : $objEvent->teaser));
        if ($objEvent->recurring)
        {
            $count     = 0;
            $arrRepeat = deserialize($objEvent->repeatEach);
            $arg       = $arrRepeat['value'];
            $unit      = $arrRepeat['unit'];
            if ($arg == 1)
            {
                $unit = substr($unit, 0, -1);
            }

            $strtotime = '+ ' . $arg . ' ' . $unit;
            $newstart  = strtotime($strtotime, $objEvent->startTime);
            $newend    = strtotime($strtotime, $objEvent->endTime);
            $freq      = 'YEARLY';
            switch ($arrRepeat['unit'])
            {
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
            if ($objEvent->recurrences > 0)
            {
                $rrule['count'] = $objEvent->recurrences;
            }
            if ($arg > 1)
            {
                $rrule['INTERVAL'] = $arg;
            }
            $vevent->setProperty('rrule', $rrule);
        }

        /*
        * begin module event_recurrences handling
        */
        if ($objEvent->repeatExecptions)
        {
            $arrSkipDates = deserialize($objEvent->repeatExecptions);
            foreach ($arrSkipDates as $skipDate)
            {
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
                $vevent->setProperty('exdate', $exdate);
            }
        }
        /*
        * end module event_recurrences handling
        */

        $ical->setComponent($vevent);
        $ical->setConfig("FILENAME", urlencode($objEvent->title) . ".ics");


        $ical->returnCalendar();
    }


    /**
     * Print an article as PDF and stream it to the browser
     */
    public function generatePdf()
    {
        Share::renderPDFModule($this->objModel, $this->strItem);
    }

    public static function renderPDFModule($objModel, $strBuffer)
    {
        ob_clean();

        global $objPage;
        $pdfPage = new PDFPage($objModel, $strBuffer);
        $renderer = $objModel->share_pdfRenderer;
        if (!empty($renderer) && $renderer == 'mpdf')
        {
            $pdfPage->mpdf = true;
        }
        $pdfPage->generate($objPage);
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
        if (isset($this->arrData[$strKey]))
        {
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