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

/**
 * Namespace
 */
namespace btv_medenlist;


/**
 * Class BTVClub
 *
 * @copyright  Riecken Software 2014
 * @author     Volker Riecken
 * @package    Devtools
 */
class BTVClub extends \Module 
{
	
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'btv_club';
	
	private function loadHttpPage($page)
	{
		$content = file_get_contents($page);
		libxml_use_internal_errors(true);
		$dom = new \DOMDocument();
		$loaded = $dom->loadHTML(utf8_decode($content));
		libxml_clear_errors();
		$dom->preserveWhiteSpace = false;
		return $dom;
	}
	
	private function setTimeFrame()
	{
		if ($this->Input->post('all'))
		{
			$this->Template->fromDate = date("d.m.Y", mktime(0, 0, 0, 5  , 1, 2014));
			$this->Template->toDate = date("d.m.Y", mktime(0, 0, 0, 7, 31, 2014));
		}
		else if ($this->Input->post('past'))
		{
			$this->Template->fromDate = date("d.m.Y", strtotime($this->Template->fromDate)-$this->past_days);
			$this->Template->toDate = date("d.m.Y", strtotime($this->Template->toDate)-$this->future_days);
		}
		else if ($this->Input->post('future'))
		{
			$this->Template->fromDate = date("d.m.Y", strtotime($this->Template->fromDate)+$this->past_days);
			$this->Template->toDate = date("d.m.Y", strtotime($this->Template->toDate)+$this->future_days);
		}
		else
		{
			$this->Template->fromDate = date("d.m.Y", mktime(0, 0, 0, date("m"), date("d")-$this->past_days, date("Y")));
			$this->Template->toDate = date("d.m.Y", mktime(0, 0, 0, date("m"), date("d")+$this->future_days, date("Y")));
		}
		if ($this->Input->post('fromDate'))
			$this->Template->fromDate = date_format(date_create($this->Input->post('fromDate')), 'd.m.Y');
		if ($this->Input->post('toDate'))
			$this->Template->toDate = date_format(date_create($this->Input->post('toDate')), 'd.m.Y');
	}
	
	private function setListFields()
	{
		$fieldNames = array('date','liga','home','guest','points','sets','games','btv_report','club_report','show','hide');
		$fields = array();
		foreach ($fieldNames as $name)
		{
			$fields[$name] = $GLOBALS['TL_LANG']['btv_frontend'][$name];
		}
		$this->Template->fields = $fields;
	}
	
	/**
	 * Generate the module
	 */
	protected function compile() 
	{
		
		if ($this->club_id != '') {	
			if ($this->Input->post('title'))
			{
				$showreport = $this->Input->post('title');
			} 

			$this->urlVerband = "http://www.btv.de";
			$this->searchClubGamesFormat = "%s/BTVToServe/abaxx-?searchTimeRange=13-4803&searchType=1&searchTimeRangeFrom=%s&searchTimeRangeTo=%s&club=%s" . 		    	"&federation=BTV&%%24part=Vereine.content.clubDaten.clubInfoRouter&theLeaguePage=b2sClubMeetings&searchMeetings=Suchen";

			$this->setListFields();
			$this->setTimeFrame();
			$this->Template->page = sprintf($this->searchClubGamesFormat, $this->urlVerband, $this->Template->fromDate, $this->Template->toDate, $this->club_id);			
			$dom = $this->loadHttpPage($this->Template->page);
			$tables = $dom->getElementsByTagName('tbody');
			$spiele = array();
			if ($tables->length > 0)
			{
				$rows = $tables->item(0)->getElementsByTagName('tr');
				foreach ($rows as $row)
				{
					$cols = $row->getElementsByTagName('td');
					if ($cols->length == 8)
					{
						$spiel = array();
						$datum = explode("/[\s\n]+/", trim($cols->item(0)->nodeValue));
						$spiel['date'] = $datum[0] . " " . substr($datum[1], 0, -5) . " " . substr($datum[1], -5);
						$spiel['liga'] = trim($cols->item(1)->nodeValue);						
						$spiel['home'] = utf8_decode(trim(str_replace("[Routenplan]", "", $cols->item(2)->nodeValue)));
						$spiel['guest'] = utf8_decode(trim($cols->item(3)->nodeValue));
						$spiel['points'] = trim($cols->item(4)->nodeValue);
						$spiel['sets'] = trim($cols->item(5)->nodeValue);
						$spiel['games'] = trim($cols->item(6)->nodeValue);
						$title = $spiel['home'] . ' - ' . $spiel['guest']; 
						if ($cols->item(7)->childNodes->length > 1)
						{
							$spiel['report'] = $this->urlVerband . $cols->item(7)->childNodes->item(1)->getAttribute('href');
						}
						$spiel['text'] = $reports[$title]['text'];
						$spiel['image'] = $reports[$title]['image'];
						$spiel['show_report'] = $showreport == $title;
						if ($spiel['show_report'])
						{
							$spiel['report_text'] = $this->Template->fields['hide'];
						}
						else
						{
							$spiel['title'] = $title;
							$spiel['report_text'] = $this->Template->fields['show'];
						} 
						$spiele[] = $spiel;
					}
				}
			}
			$this->Template->spiele = $spiele;
		}
	}
}
?>
