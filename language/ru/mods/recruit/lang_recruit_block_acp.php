<?php
/*
*
* @author admin@teksonicmods.com
* @package lang_recruit_block_acp.php
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
	'RECRUIT'							=> 'Recruitment Block',
	'RECRUIT_EXPLAIN'					=> 'Main Options configuration section for the Recruitment Block.',
	'CLASS_EXPLAIN'						=> 'Class Options configuration section for the Recruitment Block.',
	'REC_LEVEL'							=> 'Recruit Level Indicator',
	'REC_LEVEL_EXPLAIN'					=> 'Here you can choose to show which desigantor is used on the block.<br><b>Level</b> - <i>none/Low/Med/High</i><br><b>Numerical</b> - <i>0,1,2,3 etc</i><br><b>Both</b> - <i>Low (1), High (15)</i>',
	'LEVEL' 							=> 'Level',
	'NUM' 								=> 'Numerical',
	'NUM_EXPLAIN'						=> '<code>(Valid Entries: 0-99)</code>',
	'SUBMIT_EXPLAIN'					=> '<code>Any submit button saves all information entered.</code>',
	'BOTH' 								=> 'Both',
	'YES'								=> 'Yes',
	'NO'								=> 'No',
	'NONE'								=> 'None',
	'LOW'								=> 'Low',
	'MED'								=> 'Medium',
	'HIGH'								=> 'High',
	'BLOCK'								=> 'Show Block',
	'BLOCK_EXPLAIN'						=> 'This will hide the full block if you want',
	'IMAGES' 							=> 'Show Class Images',
	'IMAGES_EXPLAIN'					=> 'You can choose to disable the Class Icons on the block',
	'RECRUIT_FORUM'						=> 'Recruitment Forum',
	'RECRUIT_FORUM_EXPLAIN'				=> 'Choose the forum where your applications are posted to.',
	'CLASSCOLOR'						=> 'Use Class Colors',
	'CLASSCOLOR_EXPLAIN'				=> 'This changes the class names to be there class color.',
	'DEFAULT_LEVEL'						=> 'Default Recruit Level',
	'DEFAULT_LEVEL_EXPLAIN'				=> 'Setting this will default <strong>all</strong> classes/specs and superseeds the selections below, unless chossen selection is higher then the default.',
	'R_LINK'							=> 'Recruitment Link',
	'R_LINK_EXPLAIN'					=> 'Choose where you want the recruitment link to direct the applicant.',
	'RECRUIT_LINK'						=> 'Custom Recruit Link',
	'RECRUIT_LINK_EXPLAIN'				=> 'This needs to be a full html link. Include the http://<br /><i>ie: http://www.teksonicmods.com</i>',
		
	//Classes
	'DEATHKNIGHT'						=> 'Death Knight',
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
	'HOLY'								=> 'Holy',
	'SHADOW'							=> 'Shadow',	
	'ASSASSINATION'						=> 'Assassination',
	'COMBAT'							=> 'Combat',
	'SUBTLETY'							=> 'Subtlety',	
	'ELEMENTAL'							=> 'Elemental',
	'ENHANCEMENT'						=> 'Enhancement',
	'RESTORATION'						=> 'Restoration',	
	'AFFLICTION'						=> 'Affliction',
	'DEMONOLOGY'						=> 'Demonology',
	'DESTRUCTION'						=> 'Destruction',	
	'ARMS'								=> 'Arms',
	'FURY'								=> 'Fury',
	'PROTECTION'						=> 'Protection',
	)
);
/**
* A copy of Handyman` s MOD version check, to view it on the Raid Progress Blocks main settings
*/
$lang = array_merge($lang, array(
	'ANNOUNCEMENT_TOPIC'						=> 'Release Announcement',
	'CURRENT_VERSION'							=> 'Current Version',
	'DOWNLOAD_LATEST'							=> 'Download Latest Version',
	'LATEST_VERSION'							=> 'Latest Version',
	'NO_INFO'									=> 'Version server could not be contacted',
	'NOT_UP_TO_DATE'							=> '%s is not up to date',
	'RELEASE_ANNOUNCEMENT'						=> 'Annoucement Topic',
	'UP_TO_DATE'								=> '%s is up to date',
	'VERSION_CHECK'								=> 'Version Check',
));
?>