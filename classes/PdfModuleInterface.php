<?php

namespace HeimrichHannot\Share;

interface PdfModuleInterface
{
    /**
     * Display PDF inline or not (e.g. download it)
     *
     * @param $inline bool
     *
     * @return mixed true/false if supported, null if not supported
     */
    public function setInline($inline);

    /**
     * Set pdf font size
     *
     * @param $size int Font size
     *
     * @return mixed int fontsize or null if not supported
     */
    public function setFontSize($size);

    /**
     * Set the pdf file name without file extension.
     *
     * @param $name string
     *
     * @return mixed string filename or null if not supported
     */
    public function setFileName($name);

    /**
     * Set login information, if pdf reader cannot use cookies from browser session.
     *
     * @param string $user
     * @param string $password
     *
     * @return mixed user if success, null if not supported
     */
    public function setLoginInformation($user = '', $password = '');

    /**
     * Add Html content to render
     *
     * @param $content string
     *
     * @return mixed true if added successfully, null if not supported
     */
    public function addHtmlContent($content);

    /**
     * Render the pdf and output it
     */
    public function compile();
}