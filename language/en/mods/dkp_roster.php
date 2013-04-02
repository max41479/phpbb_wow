<?php
/**
 * @package bbDkp
 * @copyright 2011 bbdkp <https://github.com/bbDKP>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * 
 */

/**
* @ignore
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

$lang = array_merge($lang, array(

/* ACP */
'ARMORY_SELECTION' 	=> 'Armory Selection', 
'ACP_DKP_ARMORY'	=> 'Armory Import', 
'MSG_GUILD_EMPTY' 	=> 'Please give a Guild name',
'MSG_REALM_EMPTY' 	=> 'Please give a Realm name',
'MSG_INVALIDGUILD'	=> 'Invalid guild chosen',
'MSG_INVALID_CHAR'	=> 'Invalid character chosen', 
'MSG_UPDATED'	=> 'Settings updated', 

'GETACHI'			=> 'Update achievement points',
'GETACHI_EXPLAIN'	=> 'Calls character API for each member when downloading guild <span style="color:red">(this can take a time)</span>', 

'DOWNLOAD_GUILD'	=> 'Download Guild', 

'MINIMUMLEVEL' => 'Minimum level to Download:', 
'ACP_DKP_ARMORY_EXPLAIN1' => "Calls Guild API to download Guilds from Armory to your Roster. This will update the Rank structure, insert new members in the memberlist table, update existing members and set old members to inactive and rank 99. ",
'ACP_DKP_ARMORY_EXPLAIN2' => "Preselect a guild from the pulldown for update",
'ACP_DKP_ARMORY_EXPLAIN3' => "or enter a new guild to download. (this has precedence over the pulldown)",
'ACP_DKP_ARMORY_EXPLAIN4' => "Calls Character API to download individual characters from the Battlenet Armory. They will be added or updated to your member list.",
'ACP_DKP_ARMORY_EXPLAIN5' => "Preselect a guildmember from the pulldown for update",
'ACP_DKP_ARMORY_EXPLAIN6' => "or enter a new character to download. (this has precedence over the pulldown)",
'ACP_DKP_ARMORY_EXPLAIN7' => "Preselect a legion from the pulldown for update",
'ACP_DKP_ARMORY_EXPLAIN8' => "or enter a new legion to download. (this has precedence over the pulldown)",

'ACP_SUCCESSUPDGUILD' => 'Guild %s on realm %s-%s updated from Armory : <br /> members inserted : %s <br /> members updated : %s <br /> members removed : %s <br />', 
'ACP_SUCCESSADDGUILD' => 'Guild %s on realm %s-%s Inserted from Armory : <br />' . 'members inserted : %s <br />', 
'ACP_SUCCESSMEMBERADD' => 'Member inserted %s by Armory plugin', 
'ACP_SUCCESSMEMBERADDNAMED' => 'Member %s inserted %s by Armory plugin', 
'ACP_SUCCESSMEMBERUPD' => 'Member %s updated %s by Armory plugin', 
'ACP_FAILEDMEMBERUPD' => 'No changes found for member %s', 

'ACTION_NEWCHAR'	=> 'WoW Character %s added <br />',
'ACTION_UPDCHAR'	=> 'WoW Character %s Updated <br />',
'ACTION_ACHIEV'	=> 'Achievements %s Updated <br />',

/* Download titles */ 
'DOWNLOAD_TYPE_NOT_SUPPORTED' => 'Sorry, game not supported', 
'LEGION_DOWNLOAD' => 'Aion Legion Download',
'CHAR_EXISTING'=> 'Existing Member', 
'START_DOWNLOAD' => 'Download',

/* WOW */
'ARM_DOWNLOADGUILD' => 'Please confirm your download from Blizzard Armory for Guild ‘%1$s‘ from Realm ‘%2$s‘. This will update the Rank structure, insert new members, update existing members and set old members to inactive and rank 99. <br />', 
'ARM_GUILDNOTFOUND' => 'The Guild ‘%1$s‘ not found on Realm ‘%2$s‘ at site ‘%3$s‘. ',  
'ARM_GUILDDOWNLOK' => 'The Data download from Armory succeeded. Guildtag ‘%1$s‘ updated. %2$s . ' , 
'ARM_DOWNLOADCHAR' => 'Please confirm your download from the Armory for Character ‘%1$s‘ from Realm ‘%2$s‘ from site ‘%3$s‘ . ', 
'ARM_CHARDOWNOK' => 'Character ‘%1$s‘ downloaded from Armory. ', 
'ARM_CHARNOTFOUND' => 'Character ‘%1$s‘ not found on Realm ‘%2$s‘ at site ‘%3$s‘. ',
'DOWNLOAD_TYPE_WOW' => 'Blizzard Battle.net',

'ARM_STATUSLEFT' => ' left the guild : member set to inactive and rank set to ‘Out‘',
'ARM_UPDATEDTO' => ' updated to ',
'ARM_ADDED' => ' added.',
'ARM_NONEWMEMBERS' => 'No new members. ',  

'ARMORY_DOWNLOAD' => 'Guild Downloads',

'WOWNR1' => 'Guildleader', 
'WOWNR2' => 'Member' , 

'GUILD_SELECTION' => 'Guild Selection',  
'GUILD_EXISTING' => 'Existing Guildname', 
'GUILD_NAME' => 'New Guild Name',
'GUILD_DOWNLOAD' => 'Guild Download',
'REALM_NAME' => 'Realm Name',
'SITE' => 'Region',
'ARMORY_DOWNLOAD_CHAR' => 'Character Downloads',
'CHAR_NAME'	=> 'Character name',
'DOWNLOAD_CHAR'	=> 'Download WoW Character', 




));

?>