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
		// TODO: store www.anwaltverein.de in the module configuration
		$footerText = $this->objModule->share_pdfFooterText != '' ? $this->objModule->share_pdfFooterText : "www.anwaltverein.de";

		if($footerText == '') return false;

        $top = -16;

    	/*// Daten abhängig vom Dokumenttyp
    	$place = "Besucheranschrift: Bautzner Straße 17 . 01099 Dresden . Postanschrift: PF 300 200 . 01131 Dresden";
    	$color = $this->light_green;
    	$width = 3;
    	$top = -60;
		$doc = $this->DocumentType();
    	$no = $this->PageNo();

        $this->SetTextColor( 0, 0, 0);
        $sender = "<b>SACHSENMETALL Unternehmensverband der Metall- und Elektroindustrie Sachsen e. V.</b>";
        $contact = "Tel. 0351 25593-0 . Fax 0351 25593-78 . sachsenmetall@hsw-mail.de . www.sachsenmetall.org";

    	
    	$border = array( 'T' => array( 'width' => $width, 
                                       'cap' => 'butt', 
                                       'join' => 'miter', 
                                       'dash' => 0, 
                                       'color' => $color)) ;

        $this->SetY( $top);
        $this->Cell( 0, 0, "", $border, 'L', 0, '', 0, false, 'B', 'B') ;
        
        $this->SetFontSize( 8.3);

		$this->SetY( $top+ 6);
		$this->writeHTML( $sender);

		if( $doc == "kompakt") return;

        $this->SetY( $top+ 18);
        $this->writeHTML( $place);*/

        $this->setTextColorArray($this->gray);
        $this->SetY($top);

		// TODO: store
		$footerText = $this->objModule->share_pdfFooterText != '' ? $this->objModule->share_pdfFooterText : "www.anwaltverein.de";

        $this->writeHTML($footerText);
    }


	protected $last_page_flag = false;

	public function Close() {
		$this->last_page_flag = true;
		parent::Close();
	}

}

?>