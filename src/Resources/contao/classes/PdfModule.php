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


abstract class PdfModule
{

    private $module;

    /**
     * PdfModule constructor
     *
     *
     */
    public function __construct()
    {

    }

    /**
     * Display PDF inline or not (e.g. download it)
     *
     * @param $inline bool
     *
     * @return mixed true/false if supported, null if not supported
     */
    public function setInline($inline)
    {
        return null;
    }

    /**
     * Set pdf font size
     *
     * @param $size int Font size
     *
     * @return mixed int fontsize or null if not supported
     */
    public function setFontSize($size)
    {
        return null;
    }

    /**
     * Set the pdf file name without file extension.
     *
     * @param $name string
     *
     * @return mixed string filename or null if not supported
     */
    public function setFileName($name)
    {
        return null;
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
        return null;
    }

    /**
     * Render the PDF file and output it
     *
     */
    public function compile ()
    {

    }

}