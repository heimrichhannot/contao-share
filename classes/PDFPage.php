<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\Share;


use Knp\Snappy\Pdf;

class PDFPage extends PrintPage
{
    protected $fileName = 'download';
    protected $outputInline = true;
    protected $renderer = 'tcpdf';

    public function __construct($objModel, $strBuffer, array $arrConfig = [])
    {
        $this->objModel = $objModel;
        $strTemplate = $this->objModel->share_customPrintTpl;

        $renderer = $objModel->share_pdfRenderer;
        if (!empty($renderer))
        {
            $this->renderer = $renderer;
        }

        parent::__construct($strTemplate, $strBuffer, $arrConfig);
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     */
    public function setFileName(string $fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * @return bool
     */
    public function getOutputInline(): bool
    {
        return $this->outputInline;
    }

    /**
     * Set if the PDF output should be inline or download
     * @param bool $outputInline
     */
    public function setOutputInline(bool $outputInline)
    {
        $this->outputInline = $outputInline;
    }




    protected function generateHead($objPage)
    {
        return;
    }

    protected function generateOutput ($blnCheckRequest)
    {
        return $this->generatePDF($this->Template->getResponse()->getContent());
    }

    public function generatePDF ($strArticle)
    {
        ob_clean();

        // Generate article
        $strArticle = $this->replaceInsertTags($strArticle, false);
        $strArticle = html_entity_decode($strArticle, ENT_QUOTES, \Config::get('characterSet'));
        $strArticle = $this->convertRelativeUrls($strArticle, '', true);

        //Remove Links due TCPDF bug
        $strArticle = preg_replace('/<a\s.*?>(.*?)<\/a>/xsi', '${1}', $strArticle);
        // change https image src to http
        $strArticle = preg_replace('/(?<=src=\")https:/xsi', 'http:', $strArticle);




        if ($this->renderer == 'wkhtmltopdf') {
            $pdf = new Pdf(TL_ROOT.'/vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64');
            $outputInline = $this->getOutputInline() ? "inline" : "attachment";
            header('Content-Type: application/pdf');
            header('Content-Disposition: '.$outputInline.'; filename="'.$this->getFileName().'.pdf"');
            echo $pdf->getOutputFromHtml($strArticle);
        }





        if ($this->renderer === 'mpdf')
        {
            $pdf = new \mPDF();
            // Add an custom logo
            if (!empty($this->objModel->share_pdfLogoSRC))
            {
                $objModel = \FilesModel::findByUuid($this->objModel->share_pdfLogoSRC);
                if (!empty($objModel) && is_file(TL_ROOT . '/' . $objModel->path))
                {
                    $imgWidth  = 50;
                    $imgHeight = 50;
                    $imgSize = deserialize($this->objModel->share_pdfLogoSize, true);
                    if (!empty($imgSize[0]))
                    {
                        $imgWidth = $imgSize[0];
                    }
                    if (!empty($imgSize[1]))
                    {
                        $imgHeight = $imgSize[1];
                    }
                    $pdf->orig_tMargin = $this->objModel->share_pdfFontSize;
                    $pdf->margin_header = $this->objModel->share_pdfFontSize;
//                    $pdf->margin_header = $imgHeight + $this->objModel->share_pdfFontSize;
                    $pdf->setAutoTopMargin = 'pad';

                    $pdf->SetHTMLHeader('<img src="'.$objModel->path.'" width="'.$imgWidth.'" height="'.$imgHeight.'" style="margin: 0 2em 0 2em">');


                }
            }

            $pdf->WriteHTML($strArticle, 0);
            $outputInline = $this->getOutputInline() ? "I" : "D";
            $pdf->Output($this->getFileName().'.pdf', $outputInline);
        }

        // Remove form elements and JavaScript links and scripts
        $arrSearch = [
            '@<form.*</form>@Us',
            '@<a [^>]*href="[^"]*javascript:[^>]+>.*</a>@Us',
            '@<script>.*</script>@Us',
        ];

        $strArticle = preg_replace($arrSearch, '', $strArticle);

        // HOOK: allow individual PDF routines
        if (isset($GLOBALS['TL_HOOKS']['printShareItemAsPdf']) && is_array($GLOBALS['TL_HOOKS']['printShareItemAsPdf']))
        {
            foreach ($GLOBALS['TL_HOOKS']['printShareItemAsPdf'] as $callback)
            {
                $this->import($callback[0]);
                $this->{$callback[0]}->{$callback[1]}($strArticle, $this);
            }
        }

        // URL decode image paths (see #6411)
        $strArticle = preg_replace_callback(
            '@(src="[^"]+")@',
            function ($arg)
            {
                return rawurldecode($arg[0]);
            },
            $strArticle
        );

        // Handle line breaks in preformatted text
        $strArticle = preg_replace_callback(
            '@(<pre.*</pre>)@Us',
            function ($arg)
            {
                return str_replace("\n", '<br>', $arg[0]);
            },
            $strArticle
        );

        // Default PDF export using TCPDF
        $arrSearch = [
            '@<span style="text-decoration: ?underline;?">(.*)</span>@Us',
            '@(<img[^>]+>)@',
            '@(<div[^>]+block[^>]+>)@',
            '@[\n\r\t]+@',
            '@<br( /)?><div class="mod_article@',
            '@href="([^"]+)(pdf=[0-9]*(&|&amp;)?)([^"]*)"@',
        ];

        $arrReplace = [
            '<u>$1</u>',
            '<br>$1',
            '<br>$1',
            ' ',
            '<div class="mod_article',
            'href="$1$4"',
        ];

        $strArticle = preg_replace($arrSearch, $arrReplace, $strArticle);

//        $strBuffer = static::renderPrintableModule()

        // TCPDF configuration
        $l['a_meta_dir']      = 'ltr';
        $l['a_meta_charset']  = \Config::get('characterSet');
        $l['a_meta_language'] = substr($GLOBALS['TL_LANGUAGE'], 0, 2);
        $l['w_page']          = 'page';

        // Include library
//        require_once TL_ROOT . '/system/config/tcpdf.php';

        // Create new PDF document
        $pdf = new TCPDF_CustomPdf($this->objModel, PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor(PDF_AUTHOR);
        $pdf->SetTitle($this->title);
        $pdf->SetSubject($this->title);
        $pdf->SetKeywords($this->keywords);

        // Set font
        $pdf->SetFont(PDF_FONT_NAME_MAIN, '', $this->objModel->share_pdfFontSize);


        // Add custom fonts
        if ($this->objModel->share_pdfFontSRC != null)
        {
            $this->addCustomFontsToPDF($pdf, deserialize($this->objModel->share_pdfFontSRC, true));
        }

        // Prevent font subsetting (huge speed improvement)
        $pdf->setFontSubsetting(false);

        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(true);

        // Set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // Set auto page breaks
        $pdf->SetAutoPageBreak(true, 15);

        // Set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // Set some language-dependent strings
        $pdf->setLanguageArray($l);

        // Initialize document and add a page
        $pdf->AddPage();

        // Add an custom logo
        if ($this->objModel->share_pdfLogoSRC != '')
        {
            $objModel = \FilesModel::findByUuid($this->objModel->share_pdfLogoSRC);

            if ($objModel !== null && is_file(TL_ROOT . '/' . $objModel->path))
            {
                $imgWidth  = 50;
                $imgHeight = 0;

                $imgSize = deserialize($this->objModel->share_pdfLogoSize, true);

                if ($imgSize[0])
                {
                    $imgWidth = $imgSize[0];
                }

                if ($imgSize[1])
                {
                    $imgHeight = $imgSize[1];
                }

                $singleSRC = $objModel->path;

                // file, x, y, w (if 0 auto calc), h, type, link, align ...
                $pdf->ImageSVG($singleSRC, 15, 15, $imgWidth, $imgHeight, "", 'L');
                $pdf->setPageMark();
                $pdf->SetY(20);
            }
        }

        // Add an custom css
        if ($this->objModel->share_pdfCssSRC != '')
        {
            $objModel = \FilesModel::findByUuid($this->objModel->share_pdfCssSRC);

            if ($objModel !== null && is_file(TL_ROOT . '/' . $objModel->path))
            {
                $css = '<style>' . file_get_contents(TL_ROOT . '/' . $objModel->path) . '</style>';
            }
        }

        $tagvs = ['p' => [1 => ['h' => 0.0001, 'n' => 1]]];
        $pdf->setHtmlVSpace($tagvs);

        $pdf->writeHTML($css . $strArticle, false, false, true, false, '');

        // Close and output PDF document
        $pdf->lastPage();

        $title = $this->getFileName();
        // Unterstützung älterer Integrationen
        if (!empty($this->objCurrent->title)) {
            $title = standardize(ampersand($this->objCurrent->title, false));
        }
        $outputInline = $this->getOutputInline() ? "I" : "D";
        $pdf->Output( $title . '.pdf', $outputInline);

        // Stop script execution
        exit;
    }

    protected function addCustomFontsToPDF(\TCPDF &$pdf, array $arrFonts)
    {
        $objModels = \FilesModel::findMultipleByUuids($arrFonts);

        if ($objModels === null)
        {
            return false;
        }

        while ($objModels->next())
        {
            if (!file_exists(TL_ROOT . '/' . $objModels->path))
            {
                continue;
            }

            $font = \TCPDF_FONTS::addTTFfont(TL_ROOT . '/' . $objModels->path, 'TrueTypeUnicode', '', 96);
            $pdf->SetFont($font, '', $this->objModel->share_pdfFontSize ? $this->objModel->share_pdfFontSize : 13, false);
        }
    }

}