<?php
/**
*
* 
* @package language
* @version $Id: umil_profile_control_install.php,v1.0.0 2009/11/21 12:53:34  Exp $
* @copyright (c) mtrs
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

if (empty($lang) || !is_array($lang)) 
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM
//

$lang = array_merge($lang, array(
		'INSTALL_ACP_PROFILE_FIELDS'			=> 'Install Profile Fields Control MOD',
		'INSTALL_ACP_PROFILE_FIELDS_CONFIRM'	=> 'Are you ready to install the Profile Fields Control MOD Mod?',
		'ACP_PROFILE_FIELDS'					=> 'Profile Fields Control MOD',
		'ACP_PROFILE_FIELDS_EXPLAIN'			=> 'Install Profile Fields Control MOD database changes with UMIL auto method.',
		'UNINSTALL_ACP_PROFILE_FIELDS'			=> 'Uninstall Profile Fields Control MOD',
		'UNINSTALL_ACP_PROFILE_FIELDS_CONFIRM'	=> 'Are you ready to uninstall the Profile Fields Control MOD? All settings and data saved by this mod will be removed!',
		'UPDATE_ACP_PROFILE_FIELDS'				=> 'Update Profile Fields Control MOD Mod',
		'UPDATE_ACP_PROFILE_FIELDS_CONFIRM'		=> 'Are you ready to update the Profile Fields Control MOD Mod?',

));

?>