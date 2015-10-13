<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 * @package share
 * @author Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Share;


class TCPDF_CustomPdf extends \TCPDF {

	// siehe variables.less
    private $gray = array(134,138,142);

	protected $objModule;

	public function __construct($objModule, $orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=false)
	{
		$this->objModule = $objModule;
		parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
	}

    public function Header()
    {

    }
    	  

    public function Footer()
    {
		$footerText = $this->objModule->share_pdfFooterText != '' ? $this->objModule->share_pdfFooterText : '';

		if($footerText == '') return false;

        $top = -16;

        $this->setTextColorArray($this->gray);
        $this->SetY($top);
        $this->writeHTML($footerText);
    }


	protected $last_page_flag = false;

	public function Close() {
		$this->last_page_flag = true;
		parent::Close();
	}

}