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
	'ACP_PBWOW_INDEX_INFO'		=> 'Главная',
	'ACP_PBWOW_CONFIG_INFO'		=> 'Конфигурация',
	'ACP_PBWOW_ADS_INFO'		=> 'Реклама',

	// PBWoW 2
	'ACP_PBWOW2_CATEGORY'		=> 'PBWoW 2',
	'ACP_PBWOW2_OVERVIEW'		=> 'Обзор',
	'ACP_PBWOW2_CONFIG'			=> 'Конфигурация',
	'ACP_PBWOW2_POSTSTYLING'	=> 'Стиль сообщений',
	'ACP_PBWOW2_ADS'			=> 'Реклама',

	'LOG_PBWOW_CONFIG'			=> '<strong>Изменены настройки PBWoW</strong><br />&raquo; %s',
	
	// Installer
	'INSTALL_PBWOW2_MOD'			=> 'Установить PBWoW 2 MOD',
	'INSTALL_PBWOW2_MOD_CONFIRM'	=> 'Вы уверены, что хотите установить PBWoW 2 MOD?',
	'UPDATE_PBWOW2_MOD'				=> 'Обновить PBWoW 2 MOD',
	'UPDATE_PBWOW2_MOD_CONFIRM'		=> 'Вы уверены, что хотите обновить PBWoW 2 MOD?',
	'UNINSTALL_PBWOW2_MOD'			=> 'Удалить PBWoW 2 MOD',
	'UNINSTALL_PBWOW2_MOD_CONFIRM'	=> 'Вы уверены, что хотите удалить PBWoW 2 MOD?',
));

?>