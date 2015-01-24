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
 * Class BTVTeam
 *
 * @copyright  Riecken Software 2014
 * @author     Volker Riecken
 * @package    Devtools
 */
class BTVTeam extends \Module 
{
	
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'btv_team';
	
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

	private function setListFields()
	{
		$fieldNames = array('date','home','guest','points','btv_report','club_report','rank','lk','name','single','double','sum','show','hide','medencaption');
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
		if ($this->team_id != '') 
		{	
			if ($this->Input->post('title'))
			{
				$showreport = $this->Input->post('title');
			}
			
			$this->urlVerband = "http://www.btv.de";
			$this->teamPortraitFormat = "%s/BTVToServe/abaxx-?%%24part=theLeague.content.theLeaguePublic&team=%s&championship=Mittelfranken+13&theLeaguePage=teamPortrait";
			
			$this->setListFields();
			$this->Template->page = sprintf($this->teamPortraitFormat, $this->urlVerband , $this->team_id);
			$dom = $this->loadHttpPage($this->Template->page);
			$tables = $dom->getElementsByTagName('table');
			if ($tables->length > 0)
			{
				$mannschaft = array();
				$rows = $tables->item(0)->getElementsByTagName('tr');
				$cols = $rows->item(1)->getElementsByTagName('td');
				$mannschaft['liga'] = $cols->item(0)->nodeValue;
				$mannschaft['link'] = $cols->item(0)->childNodes->item(1)->getAttribute('href');
				$this->Template->mannschaft = $mannschaft;
			}
			if ($tables->length > 1)
			{
				$result = $this->Database->prepare('SELECT startTime,title,teaser,singleSRC FROM tl_calendar_events WHERE pid=?')->execute($this->btv_calendar);
				$arrEvents = $result->fetchAllAssoc();
				$events = array();
				$reports = array();
				foreach ($arrEvents as $event)
				{
					$events[$event['startTime']] = $event['title'];
					$objFile = \FilesModel::findByUuid($event['singleSRC']);
					if ($objFile)
						$reports[$event['title']] = array('text' => $event['teaser'], 'image' => $objFile->path);
					else
						$reports[$event['title']] = array('text' => $event['teaser']);
				}
				
				$spiele = array();
				$rows = $tables->item(1)->getElementsByTagName('tr');
				foreach ($rows as $row)
				{
					$cols = $row->getElementsByTagName('td');
					if ($cols->length == 5)
					{
						$spiel = array();
						$spiel['date'] = trim($cols->item(0)->nodeValue);
						$spiel['home'] = trim($cols->item(1)->nodeValue);
						$spiel['guest'] = trim($cols->item(2)->nodeValue);
						$spiel['points'] = trim($cols->item(3)->nodeValue);
						if ($cols->item(4)->childNodes->length > 1)
						{
							$spiel['report'] = $this->urlVerband . $cols->item(4)->childNodes->item(1)->getAttribute('href');
						}
						
						if ($this->btv_calendar)
						{
							$title = $spiel['home'] . ' - ' . $spiel['guest'];
							$date = date_parse($spiel['date']);
							$time = mktime($date['hour'], $date['minute'], $date['second'], $date['month'], $date['day'], $date['year']);
							$key = array_search($title, $events);
							if (!$key)
							{
								$arrNewData['pid'] = $this->btv_calendar;
								$arrNewData['startTime'] = $time;
								$arrNewData['startDate'] = $time;
								$arrNewData['title'] = $title;
								$arrNewData['addTime'] = 1;
								$arrNewData['tstamp'] = time();
								$arrNewData['published'] = 1;
								$result = $this->Database->prepare('INSERT INTO tl_calendar_events %s')->set($arrNewData)->execute();
							}
							elseif ($key != $time)
							{
								$result = $this->Database->prepare('UPDATE tl_calendar_events SET startTime=?, startDate=? WHERE title=?')->execute($time, $time, $title);
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
						}
						$spiele[] = $spiel;
					}
				}
				$this->Template->spiele = $spiele;
			}
			if ($tables->length > 2)
			{
				$spieler = array();
				$rows = $tables->item(2)->getElementsByTagName('tr');
				foreach ($rows as $row)
				{
					$cols = $row->getElementsByTagName('td');
					if ($cols->length == 9)
					{
						$spielerNeu = array();
						$spielerNeu['rank'] = trim($cols->item(0)->nodeValue);
						$spielerNeu['lk'] = trim($cols->item(1)->nodeValue);
						$spielerNeu['id'] = trim($cols->item(2)->nodeValue);
						$spielerNeu['name'] = trim($cols->item(3)->textContent);
						$spielerNeu['nation'] = trim($cols->item(4)->nodeValue);
						$spielerNeu['sg'] = trim($cols->item(5)->nodeValue);
						$spielerNeu['single'] = trim($cols->item(6)->nodeValue);
						$spielerNeu['double'] = trim($cols->item(7)->nodeValue);
						$spielerNeu['sum'] = trim($cols->item(8)->nodeValue);
						$spieler[] = $spielerNeu;
					}
				}
				$this->Template->spieler = $spieler;
			}
		}
	}		
}
?>
