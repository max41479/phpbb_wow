<?php
/**
 * bbdkp acp language file for mainmenu
 * 
 * @package bbDKP
 * @copyright 2009 bbdkp <https://github.com/bbDKP>
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

// Create the lang array if it does not already exist
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// Merge the following language entries into the lang array
$lang = array_merge($lang, array(
    'ACP_DKP_MEMBER'	    => 'Guild and Member management',
 	'ACP_DKP_GUILD_ADD'	    => 'Add Guild',  
	'ACP_DKP_GUILD_LIST'	=> 'Guilds',   
	'ACP_DKP_MEMBER_ADD'	=> 'Add member',  
	'ACP_DKP_MEMBER_LIST'	=> 'Members',
	'ACP_DKP_MEMBER_RANK'	=> 'Ranks',
	'ACP_DKP_ARMORYUPDATER'	=> 'Armory updater',	
));

?>