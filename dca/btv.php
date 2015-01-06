<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2013 Leo Feyer
 *
 * @package   btv_medenlist
 * @author    Volker Riecken
 * @license   LGPL
 * @copyright Riecken Software 2014
 */

class btv {	
	public function getClubs($objDC) {
		$arrClubs = array('06093');
		return $arrClubs;
	}
	public function getTeams($objDC) {
		if ($objDC->activeRecord->btv_club) 
		{
			$this->urlVerband = "http://www.btv.de"; 
			$this->abaxxPart = "BTVToServe/abaxx-?%24part=";
			$btv = file_get_contents($this->urlVerband . $this->abaxxPart . 'Vereine.content.clubDaten.clubInfo&$event=clubSelected&clubId=' . $objDC->activeRecord->btv_club . '&pageType=is&prevPath=Vereine.content.clubDaten.clubSearch&clubStatus=200');
			libxml_use_internal_errors(true);
			$dom = new \DOMDocument();
			$loaded = $dom->loadHTML(utf8_decode($btv));
			libxml_clear_errors();
			$dom->preserveWhiteSpace = false;  
			//http://www.btv.de/BTVToServe/abaxx-?$part=Vereine.index.menu&docPath=/BTV-Portal/Vereine/Mannschaften&nodeSel=3&docId=1034503&clubId=06093
		//http://www.btv.de/BTVToServe/abaxx-?$part=Vereine.content.clubDaten.clubInfo&$event=clubSelected&clubId=06093&pageType=is&prevPath=Vereine.content.clubDaten.clubSearch&clubStatus=200
		$arrTeams = array('Herren 50');
		return $arrTeams;
		}
	}
}
?>