<?php
/*
*
* @author admin@teksonicmods.com
* @package functions_recruit.php
* @version $Id: v2.1.0
* @copyright (c) Teksonic @ (www.teksonicmods.com)
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
* Get config value. Creates missing config entry.
*/	
function recruit_config()
{
	global $db, $cache;

	if (($recruit_config = $cache->get('recruit_config')) !== true)
	{
		$dkp = $cached_recruit_config = array();

		$sql = 'SELECT *
			FROM ' . RECRUIT_CONFIG_TABLE;
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			$cached_recruit_config[$row['config_name']] = $row['config_value'];
			$recruit_config[$row['config_name']] = $row['config_value'];
		}
		$db->sql_freeresult($result);

		$cache->put('recruit_config', $recruit_config);
	}
	return $recruit_config;
}
function class_config()
{
	global $db, $cache;

	if (($class_config = $cache->get('class_config')) !== true)
	{
		$class_config = $cached_class_config = array();

		$sql = 'SELECT config_name, config_value
			FROM ' . RECRUIT_CLASS_TABLE;
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			$cached_class_config[$row['config_name']] = $row['config_value'];
			$class_config[$row['config_name']] = $row['config_value'];
		}
		$db->sql_freeresult($result);

		$cache->put('class_config', $class_config);
	}
	return $class_config;
}
function class_config_lang()
{
	global $db, $cache;

	if (($class_config_lang = $cache->get('class_config_lang')) !== true)
	{
		$class_config_lang = $cached_class_config_lang = array();

		$sql = 'SELECT config_name, config_lang
			FROM ' . RECRUIT_CLASS_TABLE;
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			$cached_class_config_lang[$row['config_name']] = $row['config_lang'];
			$class_config_lang[$row['config_name']] = $row['config_lang'];
		}
		$db->sql_freeresult($result);

		$cache->put('class_config_lang', $class_config_lang);
	}
	return $class_config_lang;
}
function class_num_config()
{
	global $db, $cache;

	if (($class_num_config = $cache->get('class_num_config')) !== true)
	{
		$class_num_config = $cached_class_num_config = array();

		$sql = 'SELECT config_name, class_num
			FROM ' . RECRUIT_CLASS_TABLE;
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			$cached_class_num_config[$row['config_name']] = $row['class_num'];
			$class_num_config[$row['config_name']] = $row['class_num'];
		}
		$db->sql_freeresult($result);

		$cache->put('class_num_config', $class_num_config);
	}
	return $class_num_config;
}
function rb_show_class()
{
	global $db, $template, $recruit_config, $class_num_config;
		
		//Death Knight
		if ( ($recruit_config['show_dk_v'] == 1 || $recruit_config['show_dk_n'] == 1) || ($recruit_config['show_dk_v'] == 1 && $recruit_config['show_dk_n'] == 0) && ($recruit_config['show_dk_v'] == 0 && $recruit_config['show_dk_n'] == 1) )
		{
			$template->assign_var('S_DK', true);
		}
		else if ($recruit_config['show_dk_v'] == 0 && $recruit_config['show_dk_n'] == 0)
		{
			$template->assign_var('S_DK', false);
		}
		//Druid
		if ( ($recruit_config['show_druid_v'] == 1 || $recruit_config['show_druid_n'] == 1) || ($recruit_config['show_druid_v'] == 1 && $recruit_config['show_druid_n'] == 0) && ($recruit_config['show_druid_v'] == 0 && $recruit_config['show_druid_n'] == 1) )
		{
			$template->assign_var('S_DRUID', true);
		}
		else if ($recruit_config['show_druid_v'] == 0 && $recruit_config['show_druid_n'] == 0)
		{
			$template->assign_var('S_DRUID', false);
		}
		//Hunter
		if ( ($recruit_config['show_hunter_v'] == 1 || $recruit_config['show_hunter_n'] == 1) || ($recruit_config['show_hunter_v'] == 1 && $recruit_config['show_hunter_n'] == 0) && ($recruit_config['show_hunter_v'] == 0 && $recruit_config['show_hunter_n'] == 1) )
		{
			$template->assign_var('S_HUNTER', true);
		}
		else if ($recruit_config['show_hunter_v'] == 0 && $recruit_config['show_hunter_n'] == 0)
		{
			$template->assign_var('S_HUNTER', false);
		}
		//Mage
		if ( ($recruit_config['show_mage_v'] == 1 || $recruit_config['show_mage_n'] == 1) || ($recruit_config['show_mage_v'] == 1 && $recruit_config['show_mage_n'] == 0) && ($recruit_config['show_mage_v'] == 0 && $recruit_config['show_mage_n'] == 1))
		{
			$template->assign_var('S_MAGE', true);
		}
		else if ($recruit_config['show_mage_v'] == 0 && $recruit_config['show_mage_n'] == 0)
		{
			$template->assign_var('S_MAGE', false);
		}
		//Monk
		if ( ($recruit_config['show_monk_v'] == 1 || $recruit_config['show_monk_n'] == 1) || ($recruit_config['show_monk_v'] == 1 && $recruit_config['show_monk_n'] == 0) && ($recruit_config['show_monk_v'] == 0 && $recruit_config['show_monk_n'] == 1) )
		{
			$template->assign_var('S_MONK', true);
		}
		else if ($recruit_config['show_monk_v'] == 0 && $recruit_config['show_monk_n'] == 0)
		{
			$template->assign_var('S_MONK', false);
		}
		//Paladin
		if ( ($recruit_config['show_paladin_v'] == 1 || $recruit_config['show_paladin_n'] == 1) || ($recruit_config['show_paladin_v'] == 1 && $recruit_config['show_paladin_n'] == 0) && ($recruit_config['show_paladin_v'] == 0 && $recruit_config['show_paladin_n'] == 1) )
		{
			$template->assign_var('S_PALADIN', true);
		}
		else if ($recruit_config['show_paladin_v'] == 0 && $recruit_config['show_paladin_n'] == 0)
		{
			$template->assign_var('S_PALADIN', false);
		}
		//Priest
		if ( ($recruit_config['show_priest_v'] == 1 || $recruit_config['show_priest_n'] == 1) || ($recruit_config['show_priest_v'] == 1 && $recruit_config['show_priest_n'] == 0) && ($recruit_config['show_priest_v'] == 0 && $recruit_config['show_priest_n'] == 1) )
		{
			$template->assign_var('S_PRIEST', true);
		}
		else if ($recruit_config['show_priest_v'] == 0 && $recruit_config['show_priest_n'] == 0)
		{
			$template->assign_var('S_PRIEST', false);
		}
		//Rogue
		if ( ($recruit_config['show_rogue_v'] == 1 || $recruit_config['show_rogue_n'] == 1) || ($recruit_config['show_rogue_v'] == 1 && $recruit_config['show_rogue_n'] == 0) && ($recruit_config['show_rogue_v'] == 0 && $recruit_config['show_rogue_n'] == 1) )
		{
			$template->assign_var('S_ROGUE', true);
		}
		else if ($recruit_config['show_rogue_v'] == 0 && $recruit_config['show_rogue_n'] == 0)
		{
			$template->assign_var('S_ROGUE', false);
		}
		//Shaman
				if ( ($recruit_config['show_shaman_v'] == 1 || $recruit_config['show_shaman_n'] == 1) || ($recruit_config['show_shaman_v'] == 1 && $recruit_config['show_shaman_n'] == 0) && ($recruit_config['show_shaman_v'] == 0 && $recruit_config['show_shaman_n'] == 1) )
		{
			$template->assign_var('S_SHAMAN', true);
		}
		else if ($recruit_config['show_shaman_v'] == 0 && $recruit_config['show_shaman_n'] == 0)
		{
			$template->assign_var('S_SHAMAN', false);
		}
		//Warlock
			if ( ($recruit_config['show_warlock_v'] == 1 || $recruit_config['show_warlock_n'] == 1) || ($recruit_config['show_warlock_v'] == 1 && $recruit_config['show_warlock_n'] == 0) && ($recruit_config['show_warlock_v'] == 0 && $recruit_config['show_warlock_n'] == 1) )
		{
			$template->assign_var('S_WARLOCK', true);
		}
		else if ($recruit_config['show_warlock_v'] == 0 && $recruit_config['show_warlock_n'] == 0)
		{
			$template->assign_var('S_WARLOCK', false);
		}
		//Warrior
		if ( ($recruit_config['show_warrior_v'] == 1 || $recruit_config['show_warrior_n'] == 1) || ($recruit_config['show_warrior_v'] == 1 && $recruit_config['show_warrior_n'] == 0) && ($recruit_config['show_warrior_v'] == 0 && $recruit_config['show_warrior_n'] == 1) )
		{
			$template->assign_var('S_WARRIOR', true);
		}
		else if ($recruit_config['show_warrior_v'] == 0 && $recruit_config['show_warrior_n'] == 0)
		{
			$template->assign_var('S_WARRIOR', false);
		}
		//None
		if ($recruit_config['rec_level'] == 0 )
		{
			if ($recruit_config['show_dk_v'] == 0 && $recruit_config['show_druid_v'] == 0 && $recruit_config['show_hunter_v'] == 0 && $recruit_config['show_mage_v'] == 0 && $recruit_config['show_paladin_v'] == 0 && $recruit_config['show_priest_v'] == 0 && $recruit_config['show_rogue_v'] == 0 && $recruit_config['show_shaman_v'] == 0 && $recruit_config['show_warlock_v'] == 0 && $recruit_config['show_warrior_v'] == 0 && $recruit_config['show_monk_v'] == 0)
			{
			$template->assign_var('S_NONE', true);
			}
		}
		else if ($recruit_config['rec_level'] == 1 ) 
		{
			if ($recruit_config['show_dk_n'] == 0 && $recruit_config['show_druid_n'] == 0 && $recruit_config['show_hunter_n'] == 0 && $recruit_config['show_mage_n'] == 0 && $recruit_config['show_paladin_n'] == 0 && $recruit_config['show_priest_n'] == 0 && $recruit_config['show_rogue_n'] == 0 && $recruit_config['show_shaman_n'] == 0 && $recruit_config['show_warlock_n'] == 0 && $recruit_config['show_warrior_n'] == 0 && $recruit_config['show_monk_n'] == 0)
			{
			$template->assign_var('S_NONE', true);
			}
		}
		else if ($recruit_config['rec_level'] == 2 ) 
		{
			if ($recruit_config['show_dk_n'] == 0 && $recruit_config['show_druid_n'] == 0 && $recruit_config['show_hunter_n'] == 0 && $recruit_config['show_mage_n'] == 0 && $recruit_config['show_paladin_n'] == 0 && $recruit_config['show_priest_n'] == 0 && $recruit_config['show_rogue_n'] == 0 && $recruit_config['show_shaman_n'] == 0 && $recruit_config['show_warlock_n'] == 0 && $recruit_config['show_warrior_n'] == 0 && $recruit_config['show_dk_v'] == 0 && $recruit_config['show_druid_v'] == 0 && $recruit_config['show_hunter_v'] == 0 && $recruit_config['show_mage_v'] == 0 && $recruit_config['show_paladin_v'] == 0 && $recruit_config['show_priest_v'] == 0 && $recruit_config['show_rogue_v'] == 0 && $recruit_config['show_shaman_v'] == 0 && $recruit_config['show_warlock_v'] == 0 && $recruit_config['show_warrior_v'] == 0 && $recruit_config['show_monk_v'] == 0)
			{
			$template->assign_var('S_NONE', true);
			}
		}
		else
		{
			$template->assign_var('S_NONE', false);
		}
	}
?>