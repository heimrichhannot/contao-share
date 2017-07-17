<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\Share\PdfModule;


use HeimrichHannot\Share\PdfModule;
use HeimrichHannot\Share\PdfModuleInterface;
use Knp\Snappy\Pdf;

class WkhtmltopdfModule implements PdfModuleInterface
{
    protected $pdf;
    protected $inline = true;
    protected $filename = 'file';
    protected $content = [];

    function __construct()
    {
        $this->pdf = new Pdf(TL_ROOT.'/vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64');
    }


    /**
     * @param $inline bool Display PDF inline or not (e.g. download it)
     *
     * @return mixed true/false if supported, null if not supported
     */
    public function setInline($inline)
    {
        if (is_bool($inline))
        {
            $this->inline = $inline;
        }
        return $this->inline;
    }

    /**
     * @param $size int Font size
     *
     * @return mixed true if set successfully, null if not support
     */
    public function setFontSize($size)
    {
        return null;
    }

    /**
     * Set the pdf file name
     *
     * @param $name string
     *
     * @return mixed string filename or null if not supported
     */
    public function setFileName($name)
    {
        if (!empty($name))
        {
            return $this->filename = $name;
        }
       return $this->filename;
    }

    /**
     * Set login information, if pdf reader cannot use cookies from browser session.
     *
     * @param string $user
     * @param string $password
     *
     * @return mixed user if success, null if not supported
     */
    public function setLoginInformation($user = '', $password = '')
    {
        $this->pdf->setOption('username', $user);
        $this->pdf->setOption('password', $password);
    }

    /**
     * Add Html content to render
     *
     * @param $content string
     *
     * @return mixed true if added successfully, null if not supported
     */
    public function addHtmlContent($content)
    {
        $this->content[] = $content;
    }

    /**
     *
     */
    public function compile()
    {
        $pdf = $this->pdf;
        $outputInline = $this->inline ? "inline" : "attachment";
        header('Content-Type: application/pdf');
        header('Content-Disposition: '.$outputInline.'; filename="'.$this->filename.'.pdf"');
        $content = '';
        if (!empty($this->content)) {
            foreach ($this->content as $item) {
                $content .= $item;
            }
        }
        echo $pdf->getOutputFromHtml($content);
    }
}