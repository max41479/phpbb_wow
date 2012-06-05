<?php
/*
*
* @author admin@teksonicmods.com
* @package info_acp_recruit_block.php
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
	'ACP_RECRUIT_BLOCK_INFO'				=> 'Recruitment Block',
	'ACP_RB_MAIN_OPTIONS_INFO'				=> 'Main Options',
	'ACP_RB_CLASSES_INFO'					=> 'Class Options',
	
	// Logs
	'LOG_PORTAL_CONFIG'						=> '<strong>Altered Recruitment block settings</strong>',
));

?>