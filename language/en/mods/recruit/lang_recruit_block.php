<?php
/*
*
* @author admin@teksonicmods.com
* @package lang_recruit_block.php
* @version $Id: v2.0.1
* @copyright (c) Teksonic @ (www.teksonicmods.com)
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	// Recruitment
	'RECRUIT'							=> 'Recruitment',
	'REC_CLOSED'						=> 'Recruitment is currently closed! Thank you for your interest.',
		
	//Classes
	'DK'								=> 'Death Knight',
	'DRUID'								=> 'Druid',
	'HUNTER'							=> 'Hunter',
	'MAGE'								=> 'Mage',
	'PALADIN'							=> 'Paladin',
	'PRIEST'							=> 'Priest',
	'ROGUE'								=> 'Rogue',
	'SHAMAN'							=> 'Shaman',
	'WARLOCK'							=> 'Warlock',
	'WARRIOR'							=> 'Warrior',
	
	//Talents
	'BLOOD'								=> 'Blood',
	'UNHOLY'							=> 'Unholy',
	'FROST'								=> 'Frost',	
	'BALANCE'							=> 'Balance',
	'FERAL'								=> 'Feral',
	'RESTORATION'						=> 'Restoration',	
	'BEASTMASTERY'						=> 'Beast Mastery',
	'MARKSMANSHIP'						=> 'Marksmanship',
	'SURVIVAL'							=> 'Survival',	
	'ARCANE'							=> 'Arcane',
	'FIRE'								=> 'Fire',
	'FROST'								=> 'Frost',	
	'HOLY'								=> 'Holy',
	'PROTECTION'						=> 'Protection',
	'RETRIBUTION'						=> 'Retribution',	
	'DISCIPLINE'						=> 'Discipline',
	'SHADOW'							=> 'Shadow',	
	'ASSASSINATION'						=> 'Assassination',
	'COMBAT'							=> 'Combat',
	'SUBTLETY'							=> 'Subtlety',	
	'ELEMENTAL'							=> 'Elemental',
	'ENHANCEMENT'						=> 'Enhancement',
	'AFFLICTION'						=> 'Affliction',
	'DEMONOLOGY'						=> 'Demonology',
	'DESTRUCTION'						=> 'Destruction',	
	'ARMS'								=> 'Arms',
	'FURY'								=> 'Fury',
	)
);
?>