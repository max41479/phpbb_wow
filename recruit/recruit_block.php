<?php
/*
*
* @author admin@teksonicmods.com
* @package recruit_block.php
* @version $Id: v2.1.0
* @copyright (c) Teksonic @ (www.teksonicmods.com)
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include_once($phpbb_root_path . 'recruit/includes/functions_recruit.' . $phpEx);

if (!defined('IN_PHPBB'))
{
	die('Hacking attempt');
	exit;
}

$user->add_lang('mods/recruit/lang_recruit_block');
$recruit_config = recruit_config();
$class_config = class_config();
$class_num_config = class_num_config();
rb_show_class();

$template->assign_vars(array(
		//Misc Info
		'C_WH_CLASS_PATH'	=> "http://www.wowhead.com/class=",
		'VIEWFORUM'			=> "{$phpbb_root_path}viewforum.php?f=",
		'S_CLASS_COLOR'		=> $recruit_config['class_colors'],
		'S_R_IMAGES'		=> $recruit_config['show_images'],
		'REC_LINK'			=> $recruit_config['r_link'],
		'R_LINK'			=> $recruit_config['recruit_link'],
		'R_FORUM'			=> $recruit_config['recruit_forum'],
		'REC_LEVEL'			=> $recruit_config['rec_level'],
		
		//Classes
		'DK1'				=> $class_config['dk1'],
		'DK2'				=> $class_config['dk2'],
		'DK3'				=> $class_config['dk3'],
		'DRUID1'			=> $class_config['druid1'],
		'DRUID2'			=> $class_config['druid2'],
		'DRUID3'			=> $class_config['druid3'],
		'DRUID4'			=> $class_config['druid4'],
		'HUNTER1'			=> $class_config['hunter1'],
		'HUNTER2'			=> $class_config['hunter2'],
		'HUNTER3'			=> $class_config['hunter3'],
		'MAGE1'				=> $class_config['mage1'],
		'MAGE2'				=> $class_config['mage2'],
		'MAGE3'				=> $class_config['mage3'],
		'PALADIN1'			=> $class_config['paladin1'],
		'PALADIN2'			=> $class_config['paladin2'],
		'PALADIN3'			=> $class_config['paladin3'],
		'PRIEST1'			=> $class_config['priest1'],
		'PRIEST2'			=> $class_config['priest2'],
		'PRIEST3'			=> $class_config['priest3'],
		'ROGUE1'			=> $class_config['rogue1'],
		'ROGUE2'			=> $class_config['rogue2'],
		'ROGUE3'			=> $class_config['rogue3'],
		'SHAMAN1'			=> $class_config['shaman1'],
		'SHAMAN2'			=> $class_config['shaman2'],
		'SHAMAN3'			=> $class_config['shaman3'],
		'WARLOCK1'			=> $class_config['warlock1'],
		'WARLOCK2'			=> $class_config['warlock2'],
		'WARLOCK3'			=> $class_config['warlock3'],
		'WARRIOR1'			=> $class_config['warrior1'],
		'WARRIOR2'			=> $class_config['warrior2'],
		'WARRIOR3'			=> $class_config['warrior3'],
		'MONK1'			=> $class_config['monk1'],
		'MONK2'			=> $class_config['monk2'],
		'MONK3'			=> $class_config['monk3'],
		
		
		'N_DK1'				=> $class_num_config['dk1'],
		'N_DK2'				=> $class_num_config['dk2'],
		'N_DK3'				=> $class_num_config['dk3'],
		'N_DRUID1'			=> $class_num_config['druid1'],
		'N_DRUID2'			=> $class_num_config['druid2'],
		'N_DRUID3'			=> $class_num_config['druid3'],
		'N_DRUID4'			=> $class_num_config['druid4'],
		'N_HUNTER1'			=> $class_num_config['hunter1'],
		'N_HUNTER2'			=> $class_num_config['hunter2'],
		'N_HUNTER3'			=> $class_num_config['hunter3'],
		'N_MAGE1'			=> $class_num_config['mage1'],
		'N_MAGE2'			=> $class_num_config['mage2'],
		'N_MAGE3'			=> $class_num_config['mage3'],
		'N_PALADIN1'		=> $class_num_config['paladin1'],
		'N_PALADIN2'		=> $class_num_config['paladin2'],
		'N_PALADIN3'		=> $class_num_config['paladin3'],
		'N_PRIEST1'			=> $class_num_config['priest1'],
		'N_PRIEST2'			=> $class_num_config['priest2'],
		'N_PRIEST3'			=> $class_num_config['priest3'],
		'N_ROGUE1'			=> $class_num_config['rogue1'],
		'N_ROGUE2'			=> $class_num_config['rogue2'],
		'N_ROGUE3'			=> $class_num_config['rogue3'],
		'N_SHAMAN1'			=> $class_num_config['shaman1'],
		'N_SHAMAN2'			=> $class_num_config['shaman2'],
		'N_SHAMAN3'			=> $class_num_config['shaman3'],
		'N_WARLOCK1'		=> $class_num_config['warlock1'],
		'N_WARLOCK2'		=> $class_num_config['warlock2'],
		'N_WARLOCK3'		=> $class_num_config['warlock3'],
		'N_WARRIOR1'		=> $class_num_config['warrior1'],
		'N_WARRIOR2'		=> $class_num_config['warrior2'],
		'N_WARRIOR3'		=> $class_num_config['warrior3'],
		'N_MONK1'		=> $class_num_config['monk1'],
		'N_MONK2'		=> $class_num_config['monk2'],
		'N_MONK3'		=> $class_num_config['monk3'],
		
));
?>