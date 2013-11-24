<?php

if (!defined('IN_PHPBB'))
{
	exit;
}
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	// old PBWoW
	'ACP_PBWOW_INFO'			=> 'PBWoW',
	'ACP_PBWOW_INDEX_INFO'		=> 'Index',
	'ACP_PBWOW_CONFIG_INFO'		=> 'Configuration',
	'ACP_PBWOW_ADS_INFO'		=> 'Advertisements',

	// PBWoW 2
	'ACP_PBWOW2_CATEGORY'		=> 'PBWoW 2',
	'ACP_PBWOW2_OVERVIEW'		=> 'Overview',
	'ACP_PBWOW2_CONFIG'			=> 'Configuration',
	'ACP_PBWOW2_POSTSTYLING'	=> 'Post Styling',
	'ACP_PBWOW2_ADS'			=> 'Advertisements',

	'LOG_PBWOW_CONFIG'			=> '<strong>Altered PBWoW settings</strong><br />&raquo; %s',
	
	// Installer
	'INSTALL_PBWOW2_MOD'			=> 'Install PBWoW 2 MOD',
	'INSTALL_PBWOW2_MOD_CONFIRM'	=> 'Are you sure you want to install the PBWoW 2 MOD?',
	'UPDATE_PBWOW2_MOD'				=> 'Update PBWoW 2 MOD',
	'UPDATE_PBWOW2_MOD_CONFIRM'		=> 'Are you sure you want to update the PBWoW 2 MOD?',
	'UNINSTALL_PBWOW2_MOD'			=> 'Uninstall PBWoW 2 MOD',
	'UNINSTALL_PBWOW2_MOD_CONFIRM'	=> 'Are you sure you want to uninstall the PBWoW 2 MOD?',
));

?>