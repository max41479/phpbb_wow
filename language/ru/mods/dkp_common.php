<?php
/**
 * bbdkp common language file
 * 
 * @package bbDKP
 * @copyright 2009 bbdkp <https://github.com/bbDKP>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * 
 */

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* DO NOT CHANGE
*/
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}


// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, array(
'BBDKPDISABLED' => 'bbDKP is currently disabled.', 
'FOOTERBBDKP' => 'powered by bbDKP', 

//---- Portal blocks ----- 
'PORTAL'	=> 'Главная', 
'RECENTLOOT' => 'Recent Loot', 
'REMEMBERME' => 'Запомнить меня', 
'INFORUM'	=> 'Размещено в', 
'DKP'	=> 'DKP', 
'NEWS' => 'Новости',
'COMMENT' => 'Комментарии',
'VIEW_COMMENTS'	=> 'Просмотр комментариев',
'LIST_NEWS' => 'List News',
'NO_NEWS' => 'No news entries found.',
'NEWS_PER_PAGE' => 'News Entries per Page',
'ERROR_INVALID_NEWS_PROVIDED' => 'A valid news id was not provided.',
'BOSSPROGRESS' => 'Bossprogress', 	
'WELCOME'	=> 'Добро пожаловать!', 
'RECENT_LENGTH' 	=> 'Number of chars retrieved',
'NUMTOPICS'					=> 'Number of topics retrieved',
'SHOW_RT_BLOCK'				=> 'Show Recent Topics',
'RECENT_TOPICS_SETTING'		=> 'Recent Topics Settings',
'RECENT_TOPICS'				=> 'Recent Topics',	
'NO_RECENT_TOPICS'			=> 'No recent topics',
'POSTED_BY_ON'				=> 'by %1$s on %2$s',
'NEWS_POST_BY_AUTHOR'		=> 'Автор',

//links
'MENU_LINKS' => 'Weblinks',
'LINK1' => 'http://www.bbdkp.com', 
'LINK1T' => 'Powered By: bbDKP',
'LINK2' => 'http://uk.aiononline.com', 
'LINK2T' => 'Aion Online', 
'LINK3' => 'http://darkageofcamelot.com', 
'LINK3T' => 'Dark age of Camelot', 
'LINK4' => 'http://everquest2.station.sony.com/', 
'LINK4T' => 'Everquest 2', 
'LINK5' => 'http://www.playonline.com/ff11us/index.shtml', 
'LINK5T' => 'FFXI', 
'LINK6' => 'http://www.lineage2.com', 
'LINK6T' => 'Lineage 2', 
'LINK7' => 'http://www.lotro.com', 
'LINK7T' => 'Lord of the Rings', 
'LINK8' => 'http://www.riftgame.com', 
'LINK8T' => 'Rift', 
'LINK9' => 'http://www.swtor.com', 
'LINK9T' => 'Star Wars : ToR', 
'LINK10' => 'http://www.vanguardmmorpg.com', 
'LINK10T' => 'Vanguard', 
'LINK11' => 'http://www.warhammeronline.com', 
'LINK11T' => 'Warhammer', 
'LINK12' => 'http://www.worldofwarcraft.com', 
'LINK12T' => 'World of Warcraft', 

// Main Menu
'MENU' => 'Menu', 
'MENU_ADMIN_PANEL' => 'Administration Panel',
'MENU_BOSS' => 'Bossprogress',
'MENU_EVENTS' => 'Events',
'MENU_ITEMVAL' => 'Item Values',
'MENU_ITEMHIST' => 'Item History',
'MENU_NEWS' => 'Новости',
'MENU_RAIDS' => 'Raids',
'MENU_ROSTER'	=> 'Roster',
'MENU_STATS' => 'Statistics',
'MENU_SUMMARY' => 'Summary',
'MENU_STANDINGS' => 'Standings',
'MENU_VIEWMEMBER' => 'View Member',
'MENU_VIEWITEM' => 'View Item',
'MENU_VIEWRAID' => 'View Raid',
'MENU_VIEWEVENT' => 'View Event',
'MENU_PLANNER' => 'Planner',

//games supported
'WOW'        => 'World of Warcraft' , 
'LOTRO'      => 'The Lord of the Rings Online' , 
'EQ'         => 'EverQuest' , 
'DAOC'       => 'Dark Age of Camelot' , 
'VANGUARD' 	 => 'Vanguard - Saga of Heroes' , 
'EQ2'        => 'EverQuest II' , 
'WARHAMMER'  => 'Warhammer Online' ,
'AION'       => 'Aion' , 
'FFXI'       => 'Final Fantasy XI',
'RIFT'       => 'Rift',
'SWTOR'      => 'Starwars : The old Republic', 
'LINEAGE2'   => 'Lineage 2', 

//Recruitment
'RECRUITMENT_BLOCK' => 'Статус рекрутинга', 
'RECRUIT_CLOSED' => 'Закрыто !', 
'TANK' => 'Tank',
'DPS'	=> 'Dps' , 
'HEAL' 	=> 'Heal',
'RECRUIT_MESSAGE' => 'В настоящее время мы ищем новых игроков следующих классов:',
'SPEC' 	=> 'Специализация',

//ROSTER
'GUILDROSTER' => 'Guild Roster',
'CLASS' 	  => 'Класс',
'LVL' 		  => 'Level',
'ACHIEV'	  => 'Achievements', 

//listmembers
'ADJUSTMENT' => 'Adjustment',
'ALL' => 'All', 
'CURRENT' => 'Current',
'EARNED' => 'Earned',
'FILTER' => 'Filter',
'LASTRAID' => 'Last Raid',
'LEVEL' => 'Level',
'LISTMEMBERS_TITLE' => 'Member Standings',
'MNOTFOUND' => 'Could not obtain member information', 
'EMPTYRAIDNAME' => 'Raidname Not Found', 
'NAME' => 'Name',
'POOL' => 'Dkp Pool',
'RAID_ATTENDANCE_HISTORY' => 'Raid Attendance History',
'RAIDS_LIFETIME' => 'Lifetime (%s - %s)',
'ATTENDANCE_LIFETIME' => 'Lifetime Attendance',
'RAIDS_X_DAYS' => 'Last %d Days',
'SPENT' => 'Spent',
'COMPARE_MEMBERS' => 'Compare Members',
'LISTMEMBERS_FOOTCOUNT' => '... found %d members',

'LISTADJ_TITLE' => 'Group Adjustment Listing',
'LISTEVENTS_TITLE' => 'Event Values',
'LISTIADJ_TITLE' => 'Individual Adjustment Listing',
'LISTITEMS_TITLE' => 'Item Values',
'LISTPURCHASED_TITLE' => 'Item History',
'LISTRAIDS_TITLE' => 'Raids Listing',
'LOGIN_TITLE' => 'Login',
'STATS_TITLE' => '%s Stats',
'TITLE_PREFIX' => '%s %s',
'VIEWEVENT_TITLE' => 'Viewing Recorded Raid History for %s',
'VIEWITEM_TITLE' => 'Viewing Purchase History for %s',
'VIEWMEMBER_TITLE' => 'History for %s',
'VIEWRAID_TITLE' => 'Raid Summary',
'NODKPACCOUNTS'	=> 'No DKP accounts found', 
'NOUCPACCESS' => 'You are not authorised to claim characters', 
'NOUCPADDCHARS' => 'You are not authorised to add characters', 
'NOUCPUPDCHARS' => 'You are not authorised to update your characters', 
'NOUCPDELCHARS' => 'You are not authorised to delete your characters',

// Various
'ACCOUNT' => 'Account',
'ADDED_BY' => 'Added by %s',

'ADMINISTRATION' => 'Administration',
'ADMINISTRATIVE_OPTIONS' => 'Administrative Options',
'ATTENDANCE_BY_EVENT' => 'Attendance by Event',
'ATTENDED' => 'Attended',
'ATTENDEES' => 'Attendees',
'ATTENDANCE' => 'Attendance', 
'AVERAGE' => 'Average',
'BOSS' => 'Boss',
'BUYER' => 'Buyer',
'BUYERS' => 'Buyers',
'ARMOR' => 'Armor',
'TYPE' => 'Armor',

// TYPES of armor are static across games, no need to put it in DB
'CLOTH' => 'Very light / Cloth', 
'ROBE' => 'Robes', 
'LEATHER' => 'Light / Leather', 
'AUGMENTED' => 'Augmented Suit', 
'MAIL' =>  'Medium / Chain Mail', 
'HEAVY' => 'Heavy Armor', 
'PLATE' => 'Heavy / Plate', 

'CLASSID' => 'Class ID',
'CLASS_FACTOR' => 'Class Factor',
'CLASSARMOR' => 'Class Armor',
'CLASSIMAGE' => 'Image',
'CLASSMIN' => 'Min level',
'CLASSMAX' => 'Max level',
'CLASS_DISTRIBUTION' => 'Class Distribution',
'CLASS_SUMMARY' => 'Class Summary: %s to %s',
'CONFIGURATION' => 'Configuration',
'CLOSED' => 'Closed', 
'DATE' => 'Date',
'DELETE_CONFIRMATION' => 'Delete Confirmation',
'DKP_VALUE' => '%s Value',
'STATUS' => 'Status Y/N',
'CHARACTER_EXPLAIN' => 'Choose another Charactername and press submit.',
'CHARACTERS_UPDATED' => 'The Charactername %s was assigned to your account. ',
'NO_CHARACTERS' => 'No characters found to bind to your Account. Please add some characters in the ACP.',
'RAIDDECAY' => 'Raid Decay',
'ADJDECAY' => 'Adjustment decay', 
'DECAY' => 'Decay',
'DROPS' => 'Drops',
'EVENT' => 'Event',
'EVENTNAME' => 'Event Name',
'EVENTS' => 'Events',
'FACTION' => 'Faction',
'FACTIONID' => 'Faction ID',
'FIRST' => 'First',
'GROUP_ADJ' => 'Group Adj.',
'GROUP_ADJUSTMENTS' => 'Group Adjustments',
'INDIVIDUAL_ADJUSTMENTS' => 'Individual Adjustments',
'INDIVIDUAL_ADJUSTMENT_HISTORY' => 'Individual Adjustment History',
'INDIV_ADJ' => 'Indiv. Adj.',
'ITEM' => 'Item',
'ITEMS' => 'Items',
'ITEMVALUE' => 'Item Value',
'ITEMDECAY' => 'Item Decay',
'ITEMTOTAL' => 'Total value',
'ITEM_PURCHASE_HISTORY' => 'Item Purchase History',
'JOINDATE' => 'Guild Join date',
'LAST' => 'Last',
'LASTLOOT' => 'Last Loot',
'LOG_DATE_TIME' => 'Date/Time of this Log',
'LOOT_FACTOR' => 'Loot Factor',
'LOOTS' => 'Loots',
'LOOTDIST_CLASS' => 'Loot distribution per Class',
'LOW' => 'Low',  
'MANAGE' => 'Manage',
'MEDIUM' => 'Medium',
'MEMBER' => 'Member',
'NA' => 'N/A', 
'NETADJUSTMENT' => 'Net Adjustment', 
'NO_DATA' => 'No Data', 	
'RAID_ON' => 'Raid on %s on %s',
'MAX_CHARS_EXCEEDED' => 'Sorry, you can only have %s Characters bound to your phpBB account.', 
'MISCELLANEOUS' => 'Miscellaneous',
'NEWEST' => 'Newest raid',
'NOTE' => 'Note',
'OLDEST' => 'Oldest raid',
'OPEN' => 'Open',
'OUTDATE' => 'Guild leave date',
'PERCENT' => 'Percent',
'PERMISSIONS' => 'Permissions',
'PER_DAY' => 'Per Day',
'PER_RAID' => 'Per Raid',
'PCT_EARNED_LOST_TO' => '% Earned Lost to',
'PREFERENCES' => 'Preferences',
'PURCHASE_HISTORY_FOR' => 'Purchase History for %s',
'QUOTE' => 'Quote',
'RACE' => 'Race',
'RACEID' => 'Race ID',
'RAIDSTART' => 'Raid Start', 
'RAIDEND' => 'Raid End', 
'RAIDDURATION' => 'Duration', 
'RAID' => 'Raid',
'RAIDCOUNT' => 'Raidcount',
'RAIDS' => 'Raids',
'RAID_ID' => 'Raid ID',
'RAIDVALUE' => 'Raid Value',
'RAIDBONUS' => 'Raid Bonus',
'RANK_DISTRIBUTION' => 'Rank Distribution',
'RECORDED_RAID_HISTORY' => 'Raid History',
'RECORDED_DROP_HISTORY' => 'Purchase History',
'RESULT' => 'Result',
'SESSION_ID' => 'Session ID',

'SUMMARY_DATES' => 'Raid Summary: %s to %s',
'TIME_BONUS' => 'Time bonus',
'TOTAL' => 'Total',
'TIMEVALUE' => 'Time Value',
'TOTAL_EARNED' => 'Total Earned',
'TOTAL_ITEMS' => 'Total Items',
'TOTAL_RAIDS' => 'Total Raids',
'TOTAL_SPENT' => 'Total Spent',
'TRANSFER_MEMBER_HISTORY' => 'Transfer Member History',
'TYPE' => 'Type',
'UPDATE' => 'Update',
'UPDATED_BY' => 'Updated by %s',
'USER' => 'User',
'USERNAME' => 'Логин',
'VIEW' => 'View',
'VIEW_ACTION' => 'View Action',
'VIEW_LOGS' => 'View Logs',
'ZSVALUE' => 'Zerosum',
'ZEROSUM' => 'Zerosum bonus',

//lootsystems
'EP'	=> 'EP',
'EPNET'	=> 'EP net',
'GP'	=> 'GP',
'GPNET'	=> 'GP net',
'PR'	=> 'Priority',

// Page Foot Counts

'LISTEVENTS_FOOTCOUNT' => '... found %d events',
'LISTIADJ_FOOTCOUNT' => '... found %d individual adjustment(s) / %d per page',
'LISTITEMS_FOOTCOUNT' => '... found %d unique items / %d per page',
'LISTNEWS_FOOTCOUNT' => '... найдено %d Новостей',
'LISTMEMBERS_ACTIVE_FOOTCOUNT' => '... found %d active members / %sshow all</a>',
'LISTMEMBERS_COMPARE_FOOTCOUNT' => '... comparing %d members',
'LISTPURCHASED_FOOTCOUNT' => '... found %d item(s) / %d per page',
'LISTPURCHASED_FOOTCOUNT_SHORT' => '... found %d item(s)',
'LISTRAIDS_FOOTCOUNT' => '... found %d raid(s) / %d per page',
'STATS_ACTIVE_FOOTCOUNT' => '... found %d active member(s) / %sshow all</a>',
'STATS_FOOTCOUNT' => '... found %d member(s) /%sshow active</a>',
'VIEWEVENT_FOOTCOUNT' => '... found %d raid(s)',
'VIEWITEM_FOOTCOUNT' => '... found %d item(s)',
'VIEWMEMBER_ADJUSTMENT_FOOTCOUNT' => '... found %d individual adjustment(s)',
'VIEWMEMBER_ITEM_FOOTCOUNT' => '... found %d purchased item(s) / %d per page',
'VIEWMEMBER_RAID_FOOTCOUNT' => '... found %d attended raid(s) / %d per page',
'VIEWMEMBER_EVENT_FOOTCOUNT' => '... found %d attended event(s)',
'VIEWRAID_ATTENDEES_FOOTCOUNT' => '... found %d attendee(s)',
'VIEWRAID_DROPS_FOOTCOUNT' => '... found %d drop(s)',

// Submit Buttons
'LOG_ADD_DATA' => 'Add Data to Form',
'PROCEED' => 'Proceed',
'UPGRADE' => 'Upgrade',

// Form Element Descriptions
'ENDING_DATE' => 'Ending Date',
'GUILD_TAG' => 'Guild Tag',
'STARTING_DATE' => 'Starting Date',
'TO' => 'To',

// Pagination
'NEXT_PAGE' => 'Next Page',
'PAGE' => 'Page',
'PREVIOUS_PAGE' => 'Previous Page',

// Permission Messages
'NOAUTH_DEFAULT_TITLE' => 'Permission Denied',
'NOAUTH_U_EVENT_LIST' => 'You do not have permission to list events.',
'NOAUTH_U_EVENT_VIEW' => 'You do not have permission to view events.',
'NOAUTH_U_ITEM_LIST' => 'You do not have permission to list items.',
'NOAUTH_U_ITEM_VIEW' => 'You do not have permission to view items.',
'NOAUTH_U_MEMBER_LIST' => 'You do not have permission to view member standings.',
'NOAUTH_U_MEMBER_VIEW' => 'You do not have permission to view member history.',
'NOAUTH_U_RAID_LIST' => 'You do not have permission to list raids.',
'NOAUTH_U_RAID_VIEW' => 'You do not have permission to view raids.',

// Miscellaneous
'ADDED' => 'Added',
'BOSSKILLCOUNT' => 'Bosskills',
'DELETED' => 'Deleted',
'ENTER_NEW' => 'Enter New Name',
'ENTER_NEW_GAMEITEMID' => 'Game item ID',
'FEMALE'	=> 'Female',
'GENDER'	=> 'Gender',
'GUILD'	=> 'Guild', 
'LIST' => 'List',
'LIST_EVENTS' => 'List Events',
'LIST_INDIVADJ' => 'List Individual Adjustments',
'LIST_ITEMS' => 'List Items',
'LIST_MEMBERS' => 'List Members',
'LIST_RAIDS' => 'List Raids',
'MALE'	=> 'Male',
'MAY_BE_NEGATIVE_NOTE' => 'may be negative',
'NOT_AVAILABLE' => 'Not Available',
'OF_RAIDS' => '%d',
'OF_RAIDS_CHAR' => '%s', 
'OR' => 'or',
'POWERED_BY' => 'Powered by',
'REQUIRED_FIELD_NOTE' => 'Items marked with a * are required fields.',
'SELECT_EXISTING' => 'Select Existing',
'UPDATED' => 'Updated',

// Error messages
'NOT_ADMIN' => 'You are not an administrator',

//---- About --- do not change anything here 
//tabs
'ABOUT' => 'About',
'MAINIMG' => 'bbdkp.png', 
'IMAGE_ALT' => 'Logo', 
'REPOSITORY_IMAGE' => 'Google.jpg', 
'TCOPYRIGHT' => 'Copyright',  
'TCREDITS' => 'Credits', 
'TEAM' => 'Dev Team', 
'TSPONSORS' => 'Donators', 
'TPLUGINS' => 'PlugIns', 
'CREATED' => 'Created by',
'DEVELOPEDBY' => 'Developed by',
'DEVTEAM'=> 'bbDKP Development Team',
'AUTHNAME' => 'Ippeh', 
'WEBNAME' =>'Website', 
'SVNNAME' => 'Repository',
'SVNURL' => 'https://github.com/bbDKP',
'WEBURL' => 'http://www.bbdkp.com',
'PLUGINURL' => 'http://www.bbdkp.com/downloads.php?cat=2" target="_blank',
'AUTHWEB' => 'http://www.explodinglabrats.com/',
'DONATIONCOMMENT' => 'bbDKP is freeware, but you can support our development efforts by making a contribution.',
'PAYPALLINK' => '<form action="https://www.paypal.com/cgi-bin/webscr" method="post"><input type="hidden" name="cmd" value="_s-xclick"><input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
"><input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt=""><img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>',
'LICENSE1' => 'bbDKP is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   bbDKP is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with bbDKP.  If not, see http://www.gnu.org/licenses', 
'LICENSE2' => 'Powered by bbDKP (c) 2009 The bbDKP Project Team. If you use this software and find it to be useful, we ask that you retain the copyright notice below. While not required for free use, it will help build interest in the bbDKP project and is <strong>required for obtaining support</strong>.',
'COPYRIGHT3' => 'bbDKP (c) 2010 Sajaki, Malfate, Blazeflack <br />
bbDKP (c) 2008, 2009 Sajaki, Malfate, Kapli, Hroar',
'COPYRIGHT2' => 'bbDKP (c) 2007 Ippeh, Teksonic, Monkeytech, DWKN',
'COPYRIGHT1' => 'EQDkp (c) 2003 The EqDkp Project Team ',

'PRODNAME' => 'Product',
'VERSION' => 'Version',
'DEVELOPER' => 'Developer',
'JOB' => 'Job', 
'DEVLINK' => 'Link',
'PROD' => 'bbDKP',
'DEVELOPERS' => '<a href=mailto:sajaki9@gmail.com>Sajaki</a>',           	  
'PHPBB' => 'phpBB', 
'PHPBBGR' => 'phpBB Group', 
'PHPBBLINK' => 'http://www.phpbb.com', 
'EQDKP' => 'Original EQDKP',
'EQDKPVERS' => '1.3.2',
'EQDKPDEV' => 'Tsigo', 
'EQDKPLINK' => 'http://www.eqdkp.com/', 

'PLUGINS' => 	'Plugins',
'PLUGINVERS'=> 	'Version',
'MAINT'=> 		'Maintainer', 

'DONATORS' => 'Unexpectedgreg, Bisa, Sniperpaladin, McTurk, Mizpah, Kapli, Hroar, Chrome, haqzore', 

'DONATION' => 'Donation',
'DONA_NAME' => 'Name',
'ADDITIONS' => 'Code Additions',
'CONTRIB' => 'Contributions',

));

?>
