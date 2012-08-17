<?php

/*
*
* @author admin@teksonicmods.com
* @package lang_recruit_install.php
* @version $Id: v2.1.0
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
	'ACP_RECRUIT_BLOCK'							=> 'Recruitment Block',
	'ACP_RECRUIT_BLOCK_INFO'					=> 'Recruitment Block',
	'ACP_RB_MAIN_OPTIONS_INFO'					=> 'Main Options',
	'ACP_RB_CLASSES_INFO'						=> 'Class Options',

	'RAID_TOOLS_TRUE'							=> 'ACP -> Raid Tools: Already Created',
	'RAID_TOOLS_FALSE'							=> 'ACP -> Raid Tools: Created',
	'RAID_TOOLS_INFO'							=> 'ACP -> Raid Tools: Must be deleted manually if it is empty.',

));
?>