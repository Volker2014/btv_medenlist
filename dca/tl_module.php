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

$GLOBALS ['TL_DCA'] ['tl_module'] ['palettes'] ['btv_team'] = '{title_legend},name,type;{btv_auswahl},team_id;{btv_calender_legend},btv_calendar;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS ['TL_DCA'] ['tl_module'] ['palettes'] ['btv_club'] = '{title_legend},name,type;{btv_auswahl},club_id;{time_frame},past_days,future_days;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';


$GLOBALS ['TL_DCA'] ['tl_module'] ['fields'] ['team_id'] = array (
		'label' => &$GLOBALS ['TL_LANG'] ['tl_module'] ['team_id'],
		'exclude' => true,
		'inputType' => 'text',
		'sql' => "varchar(20) NULL" 
);

$GLOBALS ['TL_DCA'] ['tl_module'] ['fields'] ['club_id'] = array (
		'label' => &$GLOBALS ['TL_LANG'] ['tl_module'] ['club_id'],
		'exclude' => true,
		'inputType' => 'text',
		'sql' => "varchar(20) NULL" 
);

$GLOBALS ['TL_DCA'] ['tl_module'] ['fields'] ['past_days'] = array (
		'label' => &$GLOBALS ['TL_LANG'] ['tl_module'] ['past_days'],
		'exclude' => true,
		'inputType' => 'text',
		'sql' => "int(2) NOT NULL default '7'" 
);

$GLOBALS ['TL_DCA'] ['tl_module'] ['fields'] ['future_days'] = array (
		'label' => &$GLOBALS ['TL_LANG'] ['tl_module'] ['future_days'],
		'exclude' => true,
		'inputType' => 'text',
		'sql' => "int(2) NOT NULL default '7'" 
);

// Kalenderauswahl
$GLOBALS['TL_DCA']['tl_module']['fields']['btv_calendar'] = array 
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_module']['btv_calendar'],
	'inputType'         => 'select',
	'options_callback'  => array('tl_module_calendar', 'getCalendars'),
	'sql'           	=> "blob NULL",
	'eval'				=> array 
		(	
			'includeBlankOption' => true,
			'mandatory'		=> false,
			'multiple'		=> false
		)	
); 
?>