<?php
/*
*
* @author admin@teksonicmods.com
* @package acp_recruit_block.php
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
* @package acp
*/
class acp_recruit_block
{
    var $u_action;
    var $new_config;
	
    function main($id, $mode)
    {
		global $db, $user, $auth, $template, $cache;
		global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;
		
		$user->add_lang('mods/recruit/lang_recruit_block_acp');
		
		if (!function_exists('recruit_version_check'))
		{
			include($phpbb_root_path . '/recruit/includes/functions_recruit_version_check.' . $phpEx);
		}
		recruit_version_check();
		
		$this->u_action = append_sid("{$phpbb_root_path}adm/index.$phpEx", "i=recruit_block" );

		$action			= request_var('action', '');
		$update			= (isset($_POST['update'])) ? true : false;
		//$submit			= (isset($_POST['submit'])) ? true : false;

		switch ($mode)
		{
			case 'options':
				$this->u_action = $this->u_action . "&amp;mode=options";
				if( $update )
				{
					//Main Options
					$show_block						= request_var('show_block', 1);
					$show_images					= request_var('show_images', 1);
					$class_colors					= request_var('class_colors', 1);
					$def_level						= request_var('def_level', '');
					$rec_level						= request_var('rec_level', 2);
					$r_link							= request_var('r_link', '');
					$recruit_forum					= request_var('recruit_forum', '');
					$recruit_link					= request_var('recruit_link', '');
					
					//DB Update
					$config_name = "show_block";
					$sql = 'UPDATE ' . RECRUIT_CONFIG_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $show_block )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					
					$config_name = "show_images";
					$sql = 'UPDATE ' . RECRUIT_CONFIG_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $show_images )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					
					$config_name = "class_colors";
					$sql = 'UPDATE ' . RECRUIT_CONFIG_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $class_colors )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					
					$config_name = "def_level";
					$sql = 'UPDATE ' . RECRUIT_CONFIG_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $def_level )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					
					$config_name = "r_link";
					$sql = 'UPDATE ' . RECRUIT_CONFIG_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $r_link )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					
					$config_name = "recruit_forum";
					$sql = 'UPDATE ' . RECRUIT_CONFIG_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $recruit_forum )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					
					$config_name = "recruit_link";
					$sql = 'UPDATE ' . RECRUIT_CONFIG_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $recruit_link )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					
					$config_name = "rec_level";
					$sql = 'UPDATE ' . RECRUIT_CONFIG_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $rec_level )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
				}

				//Main Options
				$sel_s_block_yes					= '';
				$sel_s_block_no						= '';
				$sel_s_images_yes					= '';
				$sel_s_images_no					= '';
				$sel_s_ccolor_yes					= '';
				$sel_s_ccolor_no					= '';
				$sel_dl_none						= '';
				$sel_dl_low							= '';
				$sel_dl_med 						= '';
				$sel_dl_high 						= '';
				$sel_rec_level_0					= '';
				$sel_rec_level_1					= '';
				$sel_rec_level_2					= '';
				$sel_f_link 						= '';
				$sel_c_link 						= '';
				$sel_recruit_forum					= '';
				$sel_recruit_link					= '';

				$sql = 'SELECT * FROM ' . RECRUIT_CONFIG_TABLE;
				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result))
				{
					if( $row['config_name'] === 'show_block' )
					{
						if( $row['config_value'] == 1 )
						{
							$sel_s_block_yes = "checked";
						}
						else if ( $row['config_value'] == 0 )
						{
							$sel_s_block_no = "checked";
						}
					}
					else if( $row['config_name'] === 'show_images' )
					{
						if( $row['config_value'] == 1 )
						{
							$sel_s_images_yes = "checked";
						}
						else if ( $row['config_value'] == 0 )
						{
							$sel_s_images_no = "checked";
						}
					}
					else if( $row['config_name'] === 'class_colors' )
					{
						if( $row['config_value'] == 1 )
						{
							$sel_s_ccolor_yes = "checked";
						}
						else if ( $row['config_value'] == 0 )
						{
							$sel_s_ccolor_no = "checked";
						}
					}
					else if( $row['config_name'] === 'def_level' )
					{
						switch( $row['config_value'] )
						{
							case 'dl_none':
								$sel_dl_none 		= "selected='selected'";
								break;
							case 'dl_low':
								$sel_dl_low 		= "selected='selected'";
								break;
							case 'dl_med':
								$sel_dl_med	 		= "selected='selected'";
								break;
							case 'dl_high':
								$sel_dl_high	 	= "selected='selected'";
								break;
						}
					}
					else if( $row['config_name'] === 'r_link' )
					{
						switch( $row['config_value'] )
						{
							case 'f_link':
								$sel_f_link 		= "selected='selected'";
								break;
							case 'c_link':
								$sel_c_link 		= "selected='selected'";
								break;
						}
					}
					else if( $row['config_name'] === 'recruit_link' )
					{
						if( $row['config_value']  )
						{
							$sel_recruit_link = $row['config_value'];
						}
					}
					else if( $row['config_name'] === 'recruit_forum' )
					{
						if( $row['config_value']  )
						{
							$sel_recruit_forum = $row['config_value'];
						}
					}
					else if( $row['config_name'] === 'rec_level' )
					{
						if( $row['config_value'] == 0 )
						{
							$sel_rec_level_0 = "checked";
						}
						else if ( $row['config_value'] == 1 )
						{
							$sel_rec_level_1 = "checked";
						}
						else if ( $row['config_value'] == 2 )
						{
							$sel_rec_level_2 = "checked";
						}
					}
				}
				$db->sql_freeresult($result);
				
				$template->assign_vars(array(
					'SEL_BLOCK_YES'					=> $sel_s_block_yes,
					'SEL_BLOCK_NO'					=> $sel_s_block_no,
					'SEL_IMAGES_YES'				=> $sel_s_images_yes,
					'SEL_IMAGES_NO'					=> $sel_s_images_no,
					'SEL_CCOLOR_YES'				=> $sel_s_ccolor_yes,
					'SEL_CCOLOR_NO'					=> $sel_s_ccolor_no,
					'SEL_REC_LEVEL_0'				=> $sel_rec_level_0,
					'SEL_REC_LEVEL_1'				=> $sel_rec_level_1,
					'SEL_REC_LEVEL_2'				=> $sel_rec_level_2,
					'SEL_DL_NONE'					=> $sel_dl_none,
					'SEL_DL_LOW'					=> $sel_dl_low,
					'SEL_DL_MED'					=> $sel_dl_med,
					'SEL_DL_HIGH'					=> $sel_dl_high,
					'SEL_F_LINK'					=> $sel_f_link,
					'SEL_C_LINK'					=> $sel_c_link,
					'SEL_RECRUIT_FORUM'				=> $sel_recruit_forum,
					'SEL_RECRUIT_LINK'				=> $sel_recruit_link,
					'S_FORUM_OPTIONS'				=> make_forum_select(true, false, false, true),
					'U_ACTION'						=> $this->u_action,
					));

				$this->page_title = 'ACP_RB_MAIN_OPTIONS_INFO';
				$this->tpl_name = 'acp_recruit_block_options';
				return;

			break;
			
			case 'classes':
				$this->u_action = $this->u_action . "&amp;mode=classes";
				if( $update )
				{
					$dk1					= request_var('dk1', '');
					$dk2					= request_var('dk2', '');
					$dk3					= request_var('dk3', '');
					$druid1					= request_var('druid1', '');
					$druid2					= request_var('druid2', '');
					$druid3					= request_var('druid3', '');
					$druid4					= request_var('druid4', '');
					$hunter1				= request_var('hunter1', '');
					$hunter2				= request_var('hunter2', '');
					$hunter3				= request_var('hunter3', '');
					$mage1					= request_var('mage1', '');
					$mage2					= request_var('mage2', '');
					$mage3					= request_var('mage3', '');
					$paladin1				= request_var('paladin1', '');
					$paladin2				= request_var('paladin2', '');
					$paladin3				= request_var('paladin3', '');
					$priest1				= request_var('priest1', '');
					$priest2				= request_var('priest2', '');
					$priest3				= request_var('priest3', '');
					$rogue1					= request_var('rogue1', '');
					$rogue2					= request_var('rogue2', '');
					$rogue3					= request_var('rogue3', '');
					$shaman1				= request_var('shaman1', '');
					$shaman2				= request_var('shaman2', '');
					$shaman3				= request_var('shaman3', '');
					$warlock1				= request_var('warlock1', '');
					$warlock2				= request_var('warlock2', '');
					$warlock3				= request_var('warlock3', '');
					$warrior1				= request_var('warrior1', '');
					$warrior2				= request_var('warrior2', '');
					$warrior3				= request_var('warrior3', '');
					$monk1				= request_var('monk1', '');
					$monk2				= request_var('monk2', '');
					$monk3				= request_var('monk3', '');
					
					$n_dk1					= request_var('n_dk1', '');
					$n_dk2					= request_var('n_dk2', '');
					$n_dk3					= request_var('n_dk3', '');
					$n_druid1				= request_var('n_druid1', '');
					$n_druid2				= request_var('n_druid2', '');
					$n_druid3				= request_var('n_druid3', '');
					$n_druid4				= request_var('n_druid4', '');
					$n_hunter1				= request_var('n_hunter1', '');
					$n_hunter2				= request_var('n_hunter2', '');
					$n_hunter3				= request_var('n_hunter3', '');
					$n_mage1				= request_var('n_mage1', '');
					$n_mage2				= request_var('n_mage2', '');
					$n_mage3				= request_var('n_mage3', '');
					$n_paladin1				= request_var('n_paladin1', '');
					$n_paladin2				= request_var('n_paladin2', '');
					$n_paladin3				= request_var('n_paladin3', '');
					$n_priest1				= request_var('n_priest1', '');
					$n_priest2				= request_var('n_priest2', '');
					$n_priest3				= request_var('n_priest3', '');
					$n_rogue1				= request_var('n_rogue1', '');
					$n_rogue2				= request_var('n_rogue2', '');
					$n_rogue3				= request_var('n_rogue3', '');
					$n_shaman1				= request_var('n_shaman1', '');
					$n_shaman2				= request_var('n_shaman2', '');
					$n_shaman3				= request_var('n_shaman3', '');
					$n_warlock1				= request_var('n_warlock1', '');
					$n_warlock2				= request_var('n_warlock2', '');
					$n_warlock3				= request_var('n_warlock3', '');
					$n_warrior1				= request_var('n_warrior1', '');
					$n_warrior2				= request_var('n_warrior2', '');
					$n_warrior3				= request_var('n_warrior3', '');
					$n_monk1				= request_var('n_monk1', '');
					$n_monk2				= request_var('n_monk2', '');
					$n_monk3				= request_var('n_monk3', '');
					
					//Death Knight
					$config_name = "dk1";
					$class_num = "n_dk1";
					$sql = 'UPDATE ' . RECRUIT_CLASS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $dk1,
							'class_num'		=> $n_dk1 )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					$config_name = "dk2";
					$sql = 'UPDATE ' . RECRUIT_CLASS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $dk2,
							'class_num'		=> $n_dk2 )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					$config_name = "dk3";
					$sql = 'UPDATE ' . RECRUIT_CLASS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $dk3,
							'class_num'		=> $n_dk3 )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					
					$sql = 'SELECT * FROM ' . RECRUIT_CLASS_TABLE;
					$result = $db->sql_query($sql);
					while ($row = $db->sql_fetchrow($result))
					{
						if ((request_var('dk1', '') == 'None') && (request_var('dk2', '') == 'None') && (request_var('dk3', '') == 'None'))
						{
							$s_dk_v = '0';
						}
						else
						{
							$s_dk_v = '1';
						}
						$config_name = "show_dk_v";
						$sql = 'UPDATE ' . RECRUIT_CONFIG_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_value'	=> $s_dk_v )) . "
							WHERE config_name = '".$config_name."'";
						$db->sql_query($sql);
						if ((request_var('n_dk1', '') == 0) && (request_var('n_dk2', '') == 0) && (request_var('n_dk3', '') == 0))
						{
							$s_dk_n = '0';
						}
						else
						{
							$s_dk_n = '1';
						}
						$config_name = "show_dk_n";
						$sql = 'UPDATE ' . RECRUIT_CONFIG_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_value'	=> $s_dk_n )) . "
							WHERE config_name = '".$config_name."'";
						$db->sql_query($sql);
					}
					//Druid
					$config_name = "druid1";
					$class_num = "n_druid1";
					$sql = 'UPDATE ' . RECRUIT_CLASS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $druid1,
							'class_num'		=> $n_druid1 )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					$config_name = "druid2";
					$sql = 'UPDATE ' . RECRUIT_CLASS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $druid2,
							'class_num'		=> $n_druid2 )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					$config_name = "druid3";
					$sql = 'UPDATE ' . RECRUIT_CLASS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $druid3,
							'class_num'		=> $n_druid3 )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					$config_name = "druid4";
					$sql = 'UPDATE ' . RECRUIT_CLASS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $druid4,
							'class_num'		=> $n_druid4 )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					
					$sql = 'SELECT * FROM ' . RECRUIT_CLASS_TABLE;
					$result = $db->sql_query($sql);
					while ($row = $db->sql_fetchrow($result))
					{
						if ((request_var('druid1', '') == 'None') && (request_var('druid2', '') == 'None') && (request_var('druid3', '') == 'None') && (request_var('druid4', '') == 'None'))
						{
							$s_druid_v = '0';
						}
						else
						{
							$s_druid_v = '1';
						}
						$config_name = "show_druid_v";
						$sql = 'UPDATE ' . RECRUIT_CONFIG_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_value'	=> $s_druid_v )) . "
							WHERE config_name = '".$config_name."'";
						$db->sql_query($sql);
						if ((request_var('n_druid1', '') == 0) && (request_var('n_druid2', '') == 0) && (request_var('n_druid3', '') == 0) && (request_var('n_druid4', '') == 0))
						{
							$s_druid_n = '0';
						}
						else
						{
							$s_druid_n = '1';
						}
						$config_name = "show_druid_n";
						$sql = 'UPDATE ' . RECRUIT_CONFIG_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_value'	=> $s_druid_n )) . "
							WHERE config_name = '".$config_name."'";
						$db->sql_query($sql);
					}
					//Hunter
					$config_name = "hunter1";
					$sql = 'UPDATE ' . RECRUIT_CLASS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $hunter1,
							'class_num'		=> $n_hunter1 )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					$config_name = "hunter2";
					$sql = 'UPDATE ' . RECRUIT_CLASS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $hunter2,
							'class_num'		=> $n_hunter2 )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					$config_name = "hunter3";
					$sql = 'UPDATE ' . RECRUIT_CLASS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $hunter3,
							'class_num'		=> $n_hunter3 )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					
					$sql = 'SELECT * FROM ' . RECRUIT_CLASS_TABLE;
					$result = $db->sql_query($sql);
					while ($row = $db->sql_fetchrow($result))
					{
						if ((request_var('hunter1', '') == 'None') && (request_var('hunter2', '') == 'None') && (request_var('hunter3', '') == 'None'))
						{
							$s_hunter_v = '0';
						}
						else
						{
							$s_hunter_v = '1';
						}
						$config_name = "show_hunter_v";
						$sql = 'UPDATE ' . RECRUIT_CONFIG_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_value'	=> $s_hunter_v )) . "
							WHERE config_name = '".$config_name."'";
						$db->sql_query($sql);
						if ((request_var('n_hunter1', '') == 0) && (request_var('n_hunter2', '') == 0) && (request_var('n_hunter3', '') == 0))
						{
							$s_hunter_n = '0';
						}
						else
						{
							$s_hunter_n = '1';
						}
						$config_name = "show_hunter_n";
						$sql = 'UPDATE ' . RECRUIT_CONFIG_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_value'	=> $s_hunter_n )) . "
							WHERE config_name = '".$config_name."'";
						$db->sql_query($sql);
					}
					//Mage
					$config_name = "mage1";
					$sql = 'UPDATE ' . RECRUIT_CLASS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $mage1,
							'class_num'		=> $n_mage1 )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					$config_name = "mage2";
					$sql = 'UPDATE ' . RECRUIT_CLASS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $mage2,
							'class_num'		=> $n_mage2 )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					$config_name = "mage3";
					$sql = 'UPDATE ' . RECRUIT_CLASS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $mage3,
							'class_num'		=> $n_mage3 )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					
					$sql = 'SELECT * FROM ' . RECRUIT_CLASS_TABLE;
					$result = $db->sql_query($sql);
					while ($row = $db->sql_fetchrow($result))
					{
						if ((request_var('mage1', '') == 'None') && (request_var('mage2', '') == 'None') && (request_var('mage3', '') == 'None'))
						{
							$s_mage_v = '0';
						}
						else
						{
							$s_mage_v = '1';
						}
						$config_name = "show_mage_v";
						$sql = 'UPDATE ' . RECRUIT_CONFIG_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_value'	=> $s_mage_v )) . "
							WHERE config_name = '".$config_name."'";
						$db->sql_query($sql);
						if ((request_var('n_mage1', '') == 0) && (request_var('n_mage2', '') == 0) && (request_var('n_mage3', '') == 0))
						{
							$s_mage_n = '0';
						}
						else
						{
							$s_mage_n = '1';
						}
						$config_name = "show_mage_n";
						$sql = 'UPDATE ' . RECRUIT_CONFIG_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_value'	=> $s_mage_n )) . "
							WHERE config_name = '".$config_name."'";
						$db->sql_query($sql);
					}
					//Paladin
					$config_name = "paladin1";
					$sql = 'UPDATE ' . RECRUIT_CLASS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $paladin1,
							'class_num'		=> $n_paladin1 )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					$config_name = "paladin2";
					$sql = 'UPDATE ' . RECRUIT_CLASS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $paladin2,
							'class_num'		=> $n_paladin2 )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					$config_name = "paladin3";
					$sql = 'UPDATE ' . RECRUIT_CLASS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $paladin3,
							'class_num'		=> $n_paladin3 )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					
					$sql = 'SELECT * FROM ' . RECRUIT_CLASS_TABLE;
					$result = $db->sql_query($sql);
					while ($row = $db->sql_fetchrow($result))
					{
						if ((request_var('paladin1', '') == 'None') && (request_var('paladin2', '') == 'None') && (request_var('paladin3', '') == 'None'))
						{
							$s_paladin_v = '0';
						}
						else
						{
							$s_paladin_v = '1';
						}
						$config_name = "show_paladin_v";
						$sql = 'UPDATE ' . RECRUIT_CONFIG_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_value'	=> $s_paladin_v )) . "
							WHERE config_name = '".$config_name."'";
						$db->sql_query($sql);
						if ((request_var('n_paladin1', '') == 0) && (request_var('n_paladin2', '') == 0) && (request_var('n_paladin3', '') == 0))
						{
							$s_paladin_n = '0';
						}
						else
						{
							$s_paladin_n = '1';
						}
						$config_name = "show_paladin_n";
						$sql = 'UPDATE ' . RECRUIT_CONFIG_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_value'	=> $s_paladin_n )) . "
							WHERE config_name = '".$config_name."'";
						$db->sql_query($sql);
					}
					//Priest
					$config_name = "priest1";
					$sql = 'UPDATE ' . RECRUIT_CLASS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $priest1,
							'class_num'		=> $n_priest1 )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					$config_name = "priest2";
					$sql = 'UPDATE ' . RECRUIT_CLASS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $priest2,
							'class_num'		=> $n_priest2 )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					$config_name = "priest3";
					$sql = 'UPDATE ' . RECRUIT_CLASS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $priest3,
							'class_num'		=> $n_priest3 )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					
					$sql = 'SELECT * FROM ' . RECRUIT_CLASS_TABLE;
					$result = $db->sql_query($sql);
					while ($row = $db->sql_fetchrow($result))
					{
						if ((request_var('priest1', '') == 'None') && (request_var('priest2', '') == 'None') && (request_var('priest3', '') == 'None'))
						{
							$s_priest_v = '0';
						}
						else
						{
							$s_priest_v = '1';
						}
						$config_name = "show_priest_v";
						$sql = 'UPDATE ' . RECRUIT_CONFIG_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_value'	=> $s_priest_v )) . "
							WHERE config_name = '".$config_name."'";
						$db->sql_query($sql);
						if ((request_var('n_priest1', '') == 0) && (request_var('n_priest2', '') == 0) && (request_var('n_priest3', '') == 0))
						{
							$s_priest_n = '0';
						}
						else
						{
							$s_priest_n = '1';
						}
						$config_name = "show_priest_n";
						$sql = 'UPDATE ' . RECRUIT_CONFIG_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_value'	=> $s_priest_n )) . "
							WHERE config_name = '".$config_name."'";
						$db->sql_query($sql);
					}
					//Rogue
					$config_name = "rogue1";
					$sql = 'UPDATE ' . RECRUIT_CLASS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $rogue1,
							'class_num'		=> $n_rogue1 )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					$config_name = "rogue2";
					$sql = 'UPDATE ' . RECRUIT_CLASS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $rogue2,
							'class_num'		=> $n_rogue2 )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					$config_name = "rogue3";
					$sql = 'UPDATE ' . RECRUIT_CLASS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $rogue3,
							'class_num'		=> $n_rogue3 )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					
					$sql = 'SELECT * FROM ' . RECRUIT_CLASS_TABLE;
					$result = $db->sql_query($sql);
					while ($row = $db->sql_fetchrow($result))
					{
						if ((request_var('rogue1', '') == 'None') && (request_var('rogue2', '') == 'None') && (request_var('rogue3', '') == 'None'))
						{
							$s_rogue_v = '0';
						}
						else
						{
							$s_rogue_v = '1';
						}
						$config_name = "show_rogue_v";
						$sql = 'UPDATE ' . RECRUIT_CONFIG_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_value'	=> $s_rogue_v )) . "
							WHERE config_name = '".$config_name."'";
						$db->sql_query($sql);
						if ((request_var('n_rogue1', '') == 0) && (request_var('n_rogue2', '') == 0) && (request_var('n_rogue3', '') == 0))
						{
							$s_rogue_n = '0';
						}
						else
						{
							$s_rogue_n = '1';
						}
						$config_name = "show_rogue_n";
						$sql = 'UPDATE ' . RECRUIT_CONFIG_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_value'	=> $s_rogue_n )) . "
							WHERE config_name = '".$config_name."'";
						$db->sql_query($sql);
					}
					//Shaman
					$config_name = "shaman1";
					$sql = 'UPDATE ' . RECRUIT_CLASS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $shaman1,
							'class_num'		=> $n_shaman1 )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					$config_name = "shaman2";
					$sql = 'UPDATE ' . RECRUIT_CLASS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $shaman2,
							'class_num'		=> $n_shaman2 )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					$config_name = "shaman3";
					$sql = 'UPDATE ' . RECRUIT_CLASS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $shaman3,
							'class_num'		=> $n_shaman3 )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					
					$sql = 'SELECT * FROM ' . RECRUIT_CLASS_TABLE;
					$result = $db->sql_query($sql);
					while ($row = $db->sql_fetchrow($result))
					{
						if ((request_var('shaman1', '') == 'None') && (request_var('shaman2', '') == 'None') && (request_var('shaman3', '') == 'None'))
						{
							$s_shaman_v = '0';
						}
						else
						{
							$s_shaman_v = '1';
						}
						$config_name = "show_shaman_v";
						$sql = 'UPDATE ' . RECRUIT_CONFIG_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_value'	=> $s_shaman_v )) . "
							WHERE config_name = '".$config_name."'";
						$db->sql_query($sql);
						if ((request_var('n_shaman1', '') == 0) && (request_var('n_shaman2', '') == 0) && (request_var('n_shaman3', '') == 0))
						{
							$s_shaman_n = '0';
						}
						else
						{
							$s_shaman_n = '1';
						}
						$config_name = "show_shaman_n";
						$sql = 'UPDATE ' . RECRUIT_CONFIG_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_value'	=> $s_shaman_n )) . "
							WHERE config_name = '".$config_name."'";
						$db->sql_query($sql);
					}
					//Warlock
					$config_name = "warlock1";
					$sql = 'UPDATE ' . RECRUIT_CLASS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $warlock1,
							'class_num'		=> $n_warlock1 )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					$config_name = "warlock2";
					$sql = 'UPDATE ' . RECRUIT_CLASS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $warlock2,
							'class_num'		=> $n_warlock2 )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					$config_name = "warlock3";
					$sql = 'UPDATE ' . RECRUIT_CLASS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $warlock3,
							'class_num'		=> $n_warlock3 )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					
					$sql = 'SELECT * FROM ' . RECRUIT_CLASS_TABLE;
					$result = $db->sql_query($sql);
					while ($row = $db->sql_fetchrow($result))
					{
						if ((request_var('warlock1', '') == 'None') && (request_var('warlock2', '') == 'None') && (request_var('warlock3', '') == 'None'))
						{
							$s_warlock_v = '0';
						}
						else
						{
							$s_warlock_v = '1';
						}
						$config_name = "show_warlock_v";
						$sql = 'UPDATE ' . RECRUIT_CONFIG_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_value'	=> $s_warlock_v )) . "
							WHERE config_name = '".$config_name."'";
						$db->sql_query($sql);
						if ((request_var('n_warlock1', '') == 0) && (request_var('n_warlock2', '') == 0) && (request_var('n_warlock3', '') == 0))
						{
							$s_warlock_n = '0';
						}
						else
						{
							$s_warlock_n = '1';
						}
						$config_name = "show_warlock_n";
						$sql = 'UPDATE ' . RECRUIT_CONFIG_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_value'	=> $s_warlock_n )) . "
							WHERE config_name = '".$config_name."'";
						$db->sql_query($sql);
					}
					//Warrior
					$config_name = "warrior1";
					$sql = 'UPDATE ' . RECRUIT_CLASS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $warrior1,
							'class_num'		=> $n_warrior1 )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					$config_name = "warrior2";
					$sql = 'UPDATE ' . RECRUIT_CLASS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $warrior2,
							'class_num'		=> $n_warrior2 )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					$config_name = "warrior3";
					$sql = 'UPDATE ' . RECRUIT_CLASS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $warrior3,
							'class_num'		=> $n_warrior3 )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					
					$sql = 'SELECT * FROM ' . RECRUIT_CLASS_TABLE;
					$result = $db->sql_query($sql);
					while ($row = $db->sql_fetchrow($result))
					{
						if ((request_var('warrior1', '') == 'None') && (request_var('warrior2', '') == 'None') && (request_var('warrior3', '') == 'None'))
						{
							$s_warrior_v = '0';
						}
						else
						{
							$s_warrior_v = '1';
						}
						$config_name = "show_warrior_v";
						$sql = 'UPDATE ' . RECRUIT_CONFIG_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_value'	=> $s_warrior_v )) . "
							WHERE config_name = '".$config_name."'";
						$db->sql_query($sql);
						if ((request_var('n_warrior1', '') == 0) && (request_var('n_warrior2', '') == 0) && (request_var('n_warrior3', '') == 0))
						{
							$s_warrior_n = '0';
						}
						else
						{
							$s_warrior_n = '1';
						}
						$config_name = "show_warrior_n";
						$sql = 'UPDATE ' . RECRUIT_CONFIG_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_value'	=> $s_warrior_n )) . "
							WHERE config_name = '".$config_name."'";
						$db->sql_query($sql);
					}
					//monk
					$config_name = "monk1";
					$sql = 'UPDATE ' . RECRUIT_CLASS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $monk1,
							'class_num'		=> $n_monk1 )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					$config_name = "monk2";
					$sql = 'UPDATE ' . RECRUIT_CLASS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $monk2,
							'class_num'		=> $n_monk2 )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					$config_name = "monk3";
					$sql = 'UPDATE ' . RECRUIT_CLASS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_name'	=> $config_name,
							'config_value'	=> $monk3,
							'class_num'		=> $n_monk3 )) . "
							WHERE config_name = '".$config_name."'";
					$db->sql_query($sql);
					
					$sql = 'SELECT * FROM ' . RECRUIT_CLASS_TABLE;
					$result = $db->sql_query($sql);
					while ($row = $db->sql_fetchrow($result))
					{
						if ((request_var('monk1', '') == 'None') && (request_var('monk2', '') == 'None') && (request_var('monk3', '') == 'None'))
						{
							$s_monk_v = '0';
						}
						else
						{
							$s_monk_v = '1';
						}
						$config_name = "show_monk_v";
						$sql = 'UPDATE ' . RECRUIT_CONFIG_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_value'	=> $s_monk_v )) . "
							WHERE config_name = '".$config_name."'";
						$db->sql_query($sql);
						if ((request_var('n_monk1', '') == 0) && (request_var('n_monk2', '') == 0) && (request_var('n_monk3', '') == 0))
						{
							$s_monk_n = '0';
						}
						else
						{
							$s_monk_n = '1';
						}
						$config_name = "show_monk_n";
						$sql = 'UPDATE ' . RECRUIT_CONFIG_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'config_value'	=> $s_monk_n )) . "
							WHERE config_name = '".$config_name."'";
						$db->sql_query($sql);
					}
				}
			
				$sel_dk1_none						= '';
				$sel_dk1_low						= '';
				$sel_dk1_medium						= '';
				$sel_dk1_high						= '';
				$sel_dk2_none						= '';
				$sel_dk2_low						= '';
				$sel_dk2_medium						= '';
				$sel_dk2_high						= '';
				$sel_dk3_none						= '';
				$sel_dk3_low						= '';
				$sel_dk3_medium						= '';
				$sel_dk3_high						= '';
				$sel_n_dk3							= '';
				$sel_n_dk3							= '';
				$sel_n_dk3							= '';
				
				$sel_druid1_none					= '';
				$sel_druid1_low						= '';
				$sel_druid1_medium					= '';
				$sel_druid1_high					= '';
				$sel_druid2_none					= '';
				$sel_druid2_low						= '';
				$sel_druid2_medium					= '';
				$sel_druid2_high					= '';
				$sel_druid3_none					= '';
				$sel_druid3_low						= '';
				$sel_druid3_medium					= '';
				$sel_druid3_high					= '';
				$sel_druid4_none					= '';
				$sel_druid4_low						= '';
				$sel_druid4_medium					= '';
				$sel_druid4_high					= '';
				$sel_n_druid3						= '';
				$sel_n_druid3						= '';
				$sel_n_druid3						= '';
				
				$sel_hunter1_none					= '';
				$sel_hunter1_low					= '';
				$sel_hunter1_medium					= '';
				$sel_hunter1_high					= '';
				$sel_hunter2_none					= '';
				$sel_hunter2_low					= '';
				$sel_hunter2_medium					= '';
				$sel_hunter2_high					= '';
				$sel_hunter3_none					= '';
				$sel_hunter3_low					= '';
				$sel_hunter3_medium					= '';
				$sel_hunter3_high					= '';
				$sel_n_hunter3						= '';
				$sel_n_hunter3						= '';
				$sel_n_hunter3						= '';
				
				$sel_mage1_none						= '';
				$sel_mage1_low						= '';
				$sel_mage1_medium					= '';
				$sel_mage1_high						= '';
				$sel_mage2_none						= '';
				$sel_mage2_low						= '';
				$sel_mage2_medium					= '';
				$sel_mage2_high						= '';
				$sel_mage3_none						= '';
				$sel_mage3_low						= '';
				$sel_mage3_medium					= '';
				$sel_mage3_high						= '';
				$sel_n_mage3						= '';
				$sel_n_mage3						= '';
				$sel_n_mage3						= '';
				
				$sel_paladin1_none					= '';
				$sel_paladin1_low					= '';
				$sel_paladin1_medium				= '';
				$sel_paladin1_high					= '';
				$sel_paladin2_none					= '';
				$sel_paladin2_low					= '';
				$sel_paladin2_medium				= '';
				$sel_paladin2_high					= '';
				$sel_paladin3_none					= '';
				$sel_paladin3_low					= '';
				$sel_paladin3_medium				= '';
				$sel_paladin3_high					= '';
				$sel_n_paladin3						= '';
				$sel_n_paladin3						= '';
				$sel_n_paladin3						= '';
				
				$sel_priest1_none					= '';
				$sel_priest1_low					= '';
				$sel_priest1_medium					= '';
				$sel_priest1_high					= '';
				$sel_priest2_none					= '';
				$sel_priest2_low					= '';
				$sel_priest2_medium					= '';
				$sel_priest2_high					= '';
				$sel_priest3_none					= '';
				$sel_priest3_low					= '';
				$sel_priest3_medium					= '';
				$sel_priest3_high					= '';
				$sel_n_priest3						= '';
				$sel_n_priest3						= '';
				$sel_n_priest3						= '';
				
				$sel_rogue1_none					= '';
				$sel_rogue1_low						= '';
				$sel_rogue1_medium					= '';
				$sel_rogue1_high					= '';
				$sel_rogue2_none					= '';
				$sel_rogue2_low						= '';
				$sel_rogue2_medium					= '';
				$sel_rogue2_high					= '';
				$sel_rogue3_none					= '';
				$sel_rogue3_low						= '';
				$sel_rogue3_medium					= '';
				$sel_rogue3_high					= '';
				$sel_n_rogue3						= '';
				$sel_n_rogue3						= '';
				$sel_n_rogue3						= '';
				
				$sel_shaman1_none					= '';
				$sel_shaman1_low					= '';
				$sel_shaman1_medium					= '';
				$sel_shaman1_high					= '';
				$sel_shaman2_none					= '';
				$sel_shaman2_low					= '';
				$sel_shaman2_medium					= '';
				$sel_shaman2_high					= '';
				$sel_shaman3_none					= '';
				$sel_shaman3_low					= '';
				$sel_shaman3_medium					= '';
				$sel_shaman3_high					= '';
				$sel_n_shaman3						= '';
				$sel_n_shaman3						= '';
				$sel_n_shaman3						= '';
		
				$sel_warlock1_none					= '';
				$sel_warlock1_low					= '';
				$sel_warlock1_medium				= '';
				$sel_warlock1_high					= '';
				$sel_warlock2_none					= '';
				$sel_warlock2_low					= '';
				$sel_warlock2_medium				= '';
				$sel_warlock2_high					= '';
				$sel_warlock3_none					= '';
				$sel_warlock3_low					= '';
				$sel_warlock3_medium				= '';
				$sel_warlock3_high					= '';
				$sel_n_warlock3						= '';
				$sel_n_warlock3						= '';
				$sel_n_warlock3						= '';	
				
				$sel_warrior1_none					= '';
				$sel_warrior1_low					= '';
				$sel_warrior1_medium				= '';
				$sel_warrior1_high					= '';
				$sel_warrior2_none					= '';
				$sel_warrior2_low					= '';
				$sel_warrior2_medium				= '';
				$sel_warrior2_high					= '';
				$sel_warrior3_none					= '';
				$sel_warrior3_low					= '';
				$sel_warrior3_medium				= '';
				$sel_warrior3_high					= '';
				$sel_n_warrior3						= '';
				$sel_n_warrior3						= '';
				$sel_n_warrior3						= '';
				
				$sel_monk1_none					= '';
				$sel_monk1_low					= '';
				$sel_monk1_medium				= '';
				$sel_monk1_high					= '';
				$sel_monk2_none					= '';
				$sel_monk2_low					= '';
				$sel_monk2_medium				= '';
				$sel_monk2_high					= '';
				$sel_monk3_none					= '';
				$sel_monk3_low					= '';
				$sel_monk3_medium				= '';
				$sel_monk3_high					= '';
				$sel_n_monk3						= '';
				$sel_n_monk3						= '';
				$sel_n_monk3						= '';
				
				//Raid Show/Hide - Start
				$sql = 'SELECT * FROM ' . RECRUIT_CLASS_TABLE;
				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result))
				{
				//Death Knight
				if( $row['config_name'] === 'dk1' )
					{
					switch( $row['config_value'] )
						{
							case 'None':
								$sel_dk1_none 		= "selected='selected'";
								break;
							case 'Low':
								$sel_dk1_low 		= "selected='selected'";
								break;
							case 'Medium':
								$sel_dk1_medium	 	= "selected='selected'";
								break;
							case 'High':
								$sel_dk1_high	 	= "selected='selected'";
								break;
						}
						$sel_dk1_num = $row['class_num'];
					}
				if( $row['config_name'] === 'dk2' )
					{
					switch( $row['config_value'] )
						{
							case 'None':
								$sel_dk2_none 		= "selected='selected'";
								break;
							case 'Low':
								$sel_dk2_low 		= "selected='selected'";
								break;
							case 'Medium':
								$sel_dk2_medium	 	= "selected='selected'";
								break;
							case 'High':
								$sel_dk2_high	 	= "selected='selected'";
								break;
						}
						$sel_dk2_num = $row['class_num'];
					}
				if( $row['config_name'] === 'dk3' )
					{
					switch( $row['config_value'] )
						{
							case 'None':
								$sel_dk3_none 		= "selected='selected'";
								break;
							case 'Low':
								$sel_dk3_low 		= "selected='selected'";
								break;
							case 'Medium':
								$sel_dk3_medium	 	= "selected='selected'";
								break;
							case 'High':
								$sel_dk3_high	 	= "selected='selected'";
								break;
						}
						$sel_dk3_num = $row['class_num'];
					}
				//Druid
				if( $row['config_name'] === 'druid1' )
					{
					switch( $row['config_value'] )
						{
							case 'None':
								$sel_druid1_none 		= "selected='selected'";
								break;
							case 'Low':
								$sel_druid1_low 		= "selected='selected'";
								break;
							case 'Medium':
								$sel_druid1_medium	 	= "selected='selected'";
								break;
							case 'High':
								$sel_druid1_high	 	= "selected='selected'";
								break;
						}
						$sel_druid1_num = $row['class_num'];
					}
				if( $row['config_name'] === 'druid2' )
					{
					switch( $row['config_value'] )
						{
							case 'None':
								$sel_druid2_none 		= "selected='selected'";
								break;
							case 'Low':
								$sel_druid2_low 		= "selected='selected'";
								break;
							case 'Medium':
								$sel_druid2_medium	 	= "selected='selected'";
								break;
							case 'High':
								$sel_druid2_high	 	= "selected='selected'";
								break;
						}
						$sel_druid2_num = $row['class_num'];
					}
				if( $row['config_name'] === 'druid3' )
					{
					switch( $row['config_value'] )
						{
							case 'None':
								$sel_druid3_none 		= "selected='selected'";
								break;
							case 'Low':
								$sel_druid3_low 		= "selected='selected'";
								break;
							case 'Medium':
								$sel_druid3_medium	 	= "selected='selected'";
								break;
							case 'High':
								$sel_druid3_high	 	= "selected='selected'";
								break;
						}
						$sel_druid3_num = $row['class_num'];
					}
				if( $row['config_name'] === 'druid4' )
					{
					switch( $row['config_value'] )
						{
							case 'None':
								$sel_druid4_none 		= "selected='selected'";
								break;
							case 'Low':
								$sel_druid4_low 		= "selected='selected'";
								break;
							case 'Medium':
								$sel_druid4_medium	 	= "selected='selected'";
								break;
							case 'High':
								$sel_druid4_high	 	= "selected='selected'";
								break;
						}
						$sel_druid4_num = $row['class_num'];
					}
				//Hunter
				if( $row['config_name'] === 'hunter1' )
					{
					switch( $row['config_value'] )
						{
							case 'None':
								$sel_hunter1_none 		= "selected='selected'";
								break;
							case 'Low':
								$sel_hunter1_low 		= "selected='selected'";
								break;
							case 'Medium':
								$sel_hunter1_medium	 	= "selected='selected'";
								break;
							case 'High':
								$sel_hunter1_high	 	= "selected='selected'";
								break;
						}
						$sel_hunter1_num = $row['class_num'];
					}
				if( $row['config_name'] === 'hunter2' )
					{
					switch( $row['config_value'] )
						{
							case 'None':
								$sel_hunter2_none 		= "selected='selected'";
								break;
							case 'Low':
								$sel_hunter2_low 		= "selected='selected'";
								break;
							case 'Medium':
								$sel_hunter2_medium	 	= "selected='selected'";
								break;
							case 'High':
								$sel_hunter2_high	 	= "selected='selected'";
								break;
						}
						$sel_hunter2_num = $row['class_num'];
					}
				if( $row['config_name'] === 'hunter3' )
					{
					switch( $row['config_value'] )
						{
							case 'None':
								$sel_hunter3_none 		= "selected='selected'";
								break;
							case 'Low':
								$sel_hunter3_low 		= "selected='selected'";
								break;
							case 'Medium':
								$sel_hunter3_medium	 	= "selected='selected'";
								break;
							case 'High':
								$sel_hunter3_high	 	= "selected='selected'";
								break;
						}
						$sel_hunter3_num = $row['class_num'];
					}
				//Mage
				if( $row['config_name'] === 'mage1' )
					{
					switch( $row['config_value'] )
						{
							case 'None':
								$sel_mage1_none 		= "selected='selected'";
								break;
							case 'Low':
								$sel_mage1_low 		= "selected='selected'";
								break;
							case 'Medium':
								$sel_mage1_medium	 	= "selected='selected'";
								break;
							case 'High':
								$sel_mage1_high	 	= "selected='selected'";
								break;
						}
						$sel_mage1_num = $row['class_num'];
					}
				if( $row['config_name'] === 'mage2' )
					{
					switch( $row['config_value'] )
						{
							case 'None':
								$sel_mage2_none 		= "selected='selected'";
								break;
							case 'Low':
								$sel_mage2_low 		= "selected='selected'";
								break;
							case 'Medium':
								$sel_mage2_medium	 	= "selected='selected'";
								break;
							case 'High':
								$sel_mage2_high	 	= "selected='selected'";
								break;
						}
						$sel_mage2_num = $row['class_num'];
					}
				if( $row['config_name'] === 'mage3' )
					{
					switch( $row['config_value'] )
						{
							case 'None':
								$sel_mage3_none 		= "selected='selected'";
								break;
							case 'Low':
								$sel_mage3_low 		= "selected='selected'";
								break;
							case 'Medium':
								$sel_mage3_medium	 	= "selected='selected'";
								break;
							case 'High':
								$sel_mage3_high	 	= "selected='selected'";
								break;
						}
						$sel_mage3_num = $row['class_num'];
					}
				//Paladin
				if( $row['config_name'] === 'paladin1' )
					{
					switch( $row['config_value'] )
						{
							case 'None':
								$sel_paladin1_none 		= "selected='selected'";
								break;
							case 'Low':
								$sel_paladin1_low 		= "selected='selected'";
								break;
							case 'Medium':
								$sel_paladin1_medium	 	= "selected='selected'";
								break;
							case 'High':
								$sel_paladin1_high	 	= "selected='selected'";
								break;
						}
						$sel_paladin1_num = $row['class_num'];
					}
				if( $row['config_name'] === 'paladin2' )
					{
					switch( $row['config_value'] )
						{
							case 'None':
								$sel_paladin2_none 		= "selected='selected'";
								break;
							case 'Low':
								$sel_paladin2_low 		= "selected='selected'";
								break;
							case 'Medium':
								$sel_paladin2_medium	 	= "selected='selected'";
								break;
							case 'High':
								$sel_paladin2_high	 	= "selected='selected'";
								break;
						}
						$sel_paladin2_num = $row['class_num'];
					}
				if( $row['config_name'] === 'paladin3' )
					{
					switch( $row['config_value'] )
						{
							case 'None':
								$sel_paladin3_none 		= "selected='selected'";
								break;
							case 'Low':
								$sel_paladin3_low 		= "selected='selected'";
								break;
							case 'Medium':
								$sel_paladin3_medium	 	= "selected='selected'";
								break;
							case 'High':
								$sel_paladin3_high	 	= "selected='selected'";
								break;
						}
						$sel_paladin3_num = $row['class_num'];
					}
				//Priest
				if( $row['config_name'] === 'priest1' )
					{
					switch( $row['config_value'] )
						{
							case 'None':
								$sel_priest1_none 		= "selected='selected'";
								break;
							case 'Low':
								$sel_priest1_low 		= "selected='selected'";
								break;
							case 'Medium':
								$sel_priest1_medium	 	= "selected='selected'";
								break;
							case 'High':
								$sel_priest1_high	 	= "selected='selected'";
								break;
						}
						$sel_priest1_num = $row['class_num'];
					}
				if( $row['config_name'] === 'priest2' )
					{
					switch( $row['config_value'] )
						{
							case 'None':
								$sel_priest2_none 		= "selected='selected'";
								break;
							case 'Low':
								$sel_priest2_low 		= "selected='selected'";
								break;
							case 'Medium':
								$sel_priest2_medium	 	= "selected='selected'";
								break;
							case 'High':
								$sel_priest2_high	 	= "selected='selected'";
								break;
						}
						$sel_priest2_num = $row['class_num'];
					}
				if( $row['config_name'] === 'priest3' )
					{
					switch( $row['config_value'] )
						{
							case 'None':
								$sel_priest3_none 		= "selected='selected'";
								break;
							case 'Low':
								$sel_priest3_low 		= "selected='selected'";
								break;
							case 'Medium':
								$sel_priest3_medium	 	= "selected='selected'";
								break;
							case 'High':
								$sel_priest3_high	 	= "selected='selected'";
								break;
						}
						$sel_priest3_num = $row['class_num'];
					}
				//Rogue
				if( $row['config_name'] === 'rogue1' )
					{
					switch( $row['config_value'] )
						{
							case 'None':
								$sel_rogue1_none 		= "selected='selected'";
								break;
							case 'Low':
								$sel_rogue1_low 		= "selected='selected'";
								break;
							case 'Medium':
								$sel_rogue1_medium	 	= "selected='selected'";
								break;
							case 'High':
								$sel_rogue1_high	 	= "selected='selected'";
								break;
						}
						$sel_rogue1_num = $row['class_num'];
					}
				if( $row['config_name'] === 'rogue2' )
					{
					switch( $row['config_value'] )
						{
							case 'None':
								$sel_rogue2_none 		= "selected='selected'";
								break;
							case 'Low':
								$sel_rogue2_low 		= "selected='selected'";
								break;
							case 'Medium':
								$sel_rogue2_medium	 	= "selected='selected'";
								break;
							case 'High':
								$sel_rogue2_high	 	= "selected='selected'";
								break;
						}
						$sel_rogue2_num = $row['class_num'];
					}
				if( $row['config_name'] === 'rogue3' )
					{
					switch( $row['config_value'] )
						{
							case 'None':
								$sel_rogue3_none 		= "selected='selected'";
								break;
							case 'Low':
								$sel_rogue3_low 		= "selected='selected'";
								break;
							case 'Medium':
								$sel_rogue3_medium	 	= "selected='selected'";
								break;
							case 'High':
								$sel_rogue3_high	 	= "selected='selected'";
								break;
						}
						$sel_rogue3_num = $row['class_num'];
					}
				//Shaman
				if( $row['config_name'] === 'shaman1' )
					{
					switch( $row['config_value'] )
						{
							case 'None':
								$sel_shaman1_none 		= "selected='selected'";
								break;
							case 'Low':
								$sel_shaman1_low 		= "selected='selected'";
								break;
							case 'Medium':
								$sel_shaman1_medium	 	= "selected='selected'";
								break;
							case 'High':
								$sel_shaman1_high	 	= "selected='selected'";
								break;
						}
						$sel_shaman1_num = $row['class_num'];
					}
				if( $row['config_name'] === 'shaman2' )
					{
					switch( $row['config_value'] )
						{
							case 'None':
								$sel_shaman2_none 		= "selected='selected'";
								break;
							case 'Low':
								$sel_shaman2_low 		= "selected='selected'";
								break;
							case 'Medium':
								$sel_shaman2_medium	 	= "selected='selected'";
								break;
							case 'High':
								$sel_shaman2_high	 	= "selected='selected'";
								break;
						}
						$sel_shaman2_num = $row['class_num'];
					}
				if( $row['config_name'] === 'shaman3' )
					{
					switch( $row['config_value'] )
						{
							case 'None':
								$sel_shaman3_none 		= "selected='selected'";
								break;
							case 'Low':
								$sel_shaman3_low 		= "selected='selected'";
								break;
							case 'Medium':
								$sel_shaman3_medium	 	= "selected='selected'";
								break;
							case 'High':
								$sel_shaman3_high	 	= "selected='selected'";
								break;
						}
						$sel_shaman3_num = $row['class_num'];
					}
				//Warlock
				if( $row['config_name'] === 'warlock1' )
					{
					switch( $row['config_value'] )
						{
							case 'None':
								$sel_warlock1_none 		= "selected='selected'";
								break;
							case 'Low':
								$sel_warlock1_low 		= "selected='selected'";
								break;
							case 'Medium':
								$sel_warlock1_medium	 	= "selected='selected'";
								break;
							case 'High':
								$sel_warlock1_high	 	= "selected='selected'";
								break;
						}
						$sel_warlock1_num = $row['class_num'];
					}
				if( $row['config_name'] === 'warlock2' )
					{
					switch( $row['config_value'] )
						{
							case 'None':
								$sel_warlock2_none 		= "selected='selected'";
								break;
							case 'Low':
								$sel_warlock2_low 		= "selected='selected'";
								break;
							case 'Medium':
								$sel_warlock2_medium	 	= "selected='selected'";
								break;
							case 'High':
								$sel_warlock2_high	 	= "selected='selected'";
								break;
						}
						$sel_warlock2_num = $row['class_num'];
					}
				if( $row['config_name'] === 'warlock3' )
					{
					switch( $row['config_value'] )
						{
							case 'None':
								$sel_warlock3_none 		= "selected='selected'";
								break;
							case 'Low':
								$sel_warlock3_low 		= "selected='selected'";
								break;
							case 'Medium':
								$sel_warlock3_medium	 	= "selected='selected'";
								break;
							case 'High':
								$sel_warlock3_high	 	= "selected='selected'";
								break;
						}
						$sel_warlock3_num = $row['class_num'];
					}
				//Warrior
				if( $row['config_name'] === 'warrior1' )
					{
					switch( $row['config_value'] )
						{
							case 'None':
								$sel_warrior1_none 		= "selected='selected'";
								break;
							case 'Low':
								$sel_warrior1_low 		= "selected='selected'";
								break;
							case 'Medium':
								$sel_warrior1_medium	 	= "selected='selected'";
								break;
							case 'High':
								$sel_warrior1_high	 	= "selected='selected'";
								break;
						}
						$sel_warrior1_num = $row['class_num'];
					}
				if( $row['config_name'] === 'warrior2' )
					{
					switch( $row['config_value'] )
						{
							case 'None':
								$sel_warrior2_none 		= "selected='selected'";
								break;
							case 'Low':
								$sel_warrior2_low 		= "selected='selected'";
								break;
							case 'Medium':
								$sel_warrior2_medium	 	= "selected='selected'";
								break;
							case 'High':
								$sel_warrior2_high	 	= "selected='selected'";
								break;
						}
						$sel_warrior2_num = $row['class_num'];
					}
				if( $row['config_name'] === 'warrior3' )
					{
					switch( $row['config_value'] )
						{
							case 'None':
								$sel_warrior3_none 		= "selected='selected'";
								break;
							case 'Low':
								$sel_warrior3_low 		= "selected='selected'";
								break;
							case 'Medium':
								$sel_warrior3_medium	 	= "selected='selected'";
								break;
							case 'High':
								$sel_warrior3_high	 	= "selected='selected'";
								break;
						}
						$sel_warrior3_num = $row['class_num'];
					}
					//monk
				if( $row['config_name'] === 'monk1' )
					{
					switch( $row['config_value'] )
						{
							case 'None':
								$sel_monk1_none 		= "selected='selected'";
								break;
							case 'Low':
								$sel_monk1_low 		= "selected='selected'";
								break;
							case 'Medium':
								$sel_monk1_medium	 	= "selected='selected'";
								break;
							case 'High':
								$sel_monk1_high	 	= "selected='selected'";
								break;
						}
						$sel_monk1_num = $row['class_num'];
					}
				if( $row['config_name'] === 'monk2' )
					{
					switch( $row['config_value'] )
						{
							case 'None':
								$sel_monk2_none 		= "selected='selected'";
								break;
							case 'Low':
								$sel_monk2_low 		= "selected='selected'";
								break;
							case 'Medium':
								$sel_monk2_medium	 	= "selected='selected'";
								break;
							case 'High':
								$sel_monk2_high	 	= "selected='selected'";
								break;
						}
						$sel_monk2_num = $row['class_num'];
					}
				if( $row['config_name'] === 'monk3' )
					{
					switch( $row['config_value'] )
						{
							case 'None':
								$sel_monk3_none 		= "selected='selected'";
								break;
							case 'Low':
								$sel_monk3_low 		= "selected='selected'";
								break;
							case 'Medium':
								$sel_monk3_medium	 	= "selected='selected'";
								break;
							case 'High':
								$sel_monk3_high	 	= "selected='selected'";
								break;
						}
						$sel_monk3_num = $row['class_num'];
					}
				}
				$db->sql_freeresult($result);
				
				$template->assign_vars(array(
					'SEL_DK1_NONE'					=> $sel_dk1_none,
					'SEL_DK1_LOW'					=> $sel_dk1_low,
					'SEL_DK1_MEDIUM'				=> $sel_dk1_medium,
					'SEL_DK1_HIGH'					=> $sel_dk1_high,
					'SEL_DK2_NONE'					=> $sel_dk2_none,
					'SEL_DK2_LOW'					=> $sel_dk2_low,
					'SEL_DK2_MEDIUM'				=> $sel_dk2_medium,
					'SEL_DK2_HIGH'					=> $sel_dk2_high,
					'SEL_DK3_NONE'					=> $sel_dk3_none,
					'SEL_DK3_LOW'					=> $sel_dk3_low,
					'SEL_DK3_MEDIUM'				=> $sel_dk3_medium,
					'SEL_DK3_HIGH'					=> $sel_dk3_high,
					'SEL_DK1_NUM'					=> $sel_dk1_num,
					'SEL_DK2_NUM'					=> $sel_dk2_num,
					'SEL_DK3_NUM'					=> $sel_dk3_num,
					
					'SEL_DRUID1_NONE'				=> $sel_druid1_none,
					'SEL_DRUID1_LOW'				=> $sel_druid1_low,
					'SEL_DRUID1_MEDIUM'				=> $sel_druid1_medium,
					'SEL_DRUID1_HIGH'				=> $sel_druid1_high,
					'SEL_DRUID2_NONE'				=> $sel_druid2_none,
					'SEL_DRUID2_LOW'				=> $sel_druid2_low,
					'SEL_DRUID2_MEDIUM'				=> $sel_druid2_medium,
					'SEL_DRUID2_HIGH'				=> $sel_druid2_high,
					'SEL_DRUID3_NONE'				=> $sel_druid3_none,
					'SEL_DRUID3_LOW'				=> $sel_druid3_low,
					'SEL_DRUID3_MEDIUM'				=> $sel_druid3_medium,
					'SEL_DRUID3_HIGH'				=> $sel_druid3_high,
					'SEL_DRUID4_NONE'				=> $sel_druid4_none,
					'SEL_DRUID4_LOW'				=> $sel_druid4_low,
					'SEL_DRUID4_MEDIUM'				=> $sel_druid4_medium,
					'SEL_DRUID4_HIGH'				=> $sel_druid4_high,
					'SEL_DRUID1_NUM'				=> $sel_druid1_num,
					'SEL_DRUID2_NUM'				=> $sel_druid2_num,
					'SEL_DRUID3_NUM'				=> $sel_druid3_num,
					'SEL_DRUID4_NUM'				=> $sel_druid4_num,
					
					'SEL_HUNTER1_NONE'				=> $sel_hunter1_none,
					'SEL_HUNTER1_LOW'				=> $sel_hunter1_low,
					'SEL_HUNTER1_MEDIUM'			=> $sel_hunter1_medium,
					'SEL_HUNTER1_HIGH'				=> $sel_hunter1_high,
					'SEL_HUNTER2_NONE'				=> $sel_hunter2_none,
					'SEL_HUNTER2_LOW'				=> $sel_hunter2_low,
					'SEL_HUNTER2_MEDIUM'			=> $sel_hunter2_medium,
					'SEL_HUNTER2_HIGH'				=> $sel_hunter2_high,
					'SEL_HUNTER3_NONE'				=> $sel_hunter3_none,
					'SEL_HUNTER3_LOW'				=> $sel_hunter3_low,
					'SEL_HUNTER3_MEDIUM'			=> $sel_hunter3_medium,
					'SEL_HUNTER3_HIGH'				=> $sel_hunter3_high,
					'SEL_HUNTER1_NUM'				=> $sel_hunter1_num,
					'SEL_HUNTER2_NUM'				=> $sel_hunter2_num,
					'SEL_HUNTER3_NUM'				=> $sel_hunter3_num,
					
					'SEL_MAGE1_NONE'				=> $sel_mage1_none,
					'SEL_MAGE1_LOW'					=> $sel_mage1_low,
					'SEL_MAGE1_MEDIUM'				=> $sel_mage1_medium,
					'SEL_MAGE1_HIGH'				=> $sel_mage1_high,
					'SEL_MAGE2_NONE'				=> $sel_mage2_none,
					'SEL_MAGE2_LOW'					=> $sel_mage2_low,
					'SEL_MAGE2_MEDIUM'				=> $sel_mage2_medium,
					'SEL_MAGE2_HIGH'				=> $sel_mage2_high,
					'SEL_MAGE3_NONE'				=> $sel_mage3_none,
					'SEL_MAGE3_LOW'					=> $sel_mage3_low,
					'SEL_MAGE3_MEDIUM'				=> $sel_mage3_medium,
					'SEL_MAGE3_HIGH'				=> $sel_mage3_high,
					'SEL_MAGE1_NUM'					=> $sel_mage1_num,
					'SEL_MAGE2_NUM'					=> $sel_mage2_num,
					'SEL_MAGE3_NUM'					=> $sel_mage3_num,	
					
					'SEL_PALADIN1_NONE'				=> $sel_paladin1_none,
					'SEL_PALADIN1_LOW'				=> $sel_paladin1_low,
					'SEL_PALADIN1_MEDIUM'			=> $sel_paladin1_medium,
					'SEL_PALADIN1_HIGH'				=> $sel_paladin1_high,
					'SEL_PALADIN2_NONE'				=> $sel_paladin2_none,
					'SEL_PALADIN2_LOW'				=> $sel_paladin2_low,
					'SEL_PALADIN2_MEDIUM'			=> $sel_paladin2_medium,
					'SEL_PALADIN2_HIGH'				=> $sel_paladin2_high,
					'SEL_PALADIN3_NONE'				=> $sel_paladin3_none,
					'SEL_PALADIN3_LOW'				=> $sel_paladin3_low,
					'SEL_PALADIN3_MEDIUM'			=> $sel_paladin3_medium,
					'SEL_PALADIN3_HIGH'				=> $sel_paladin3_high,
					'SEL_PALADIN1_NUM'				=> $sel_paladin1_num,
					'SEL_PALADIN2_NUM'				=> $sel_paladin2_num,
					'SEL_PALADIN3_NUM'				=> $sel_paladin3_num,
					
					'SEL_PRIEST1_NONE'				=> $sel_priest1_none,
					'SEL_PRIEST1_LOW'				=> $sel_priest1_low,
					'SEL_PRIEST1_MEDIUM'			=> $sel_priest1_medium,
					'SEL_PRIEST1_HIGH'				=> $sel_priest1_high,
					'SEL_PRIEST2_NONE'				=> $sel_priest2_none,
					'SEL_PRIEST2_LOW'				=> $sel_priest2_low,
					'SEL_PRIEST2_MEDIUM'			=> $sel_priest2_medium,
					'SEL_PRIEST2_HIGH'				=> $sel_priest2_high,
					'SEL_PRIEST3_NONE'				=> $sel_priest3_none,
					'SEL_PRIEST3_LOW'				=> $sel_priest3_low,
					'SEL_PRIEST3_MEDIUM'			=> $sel_priest3_medium,
					'SEL_PRIEST3_HIGH'				=> $sel_priest3_high,
					'SEL_PRIEST1_NUM'				=> $sel_priest1_num,
					'SEL_PRIEST2_NUM'				=> $sel_priest2_num,
					'SEL_PRIEST3_NUM'				=> $sel_priest3_num,
					
					'SEL_ROGUE1_NONE'				=> $sel_rogue1_none,
					'SEL_ROGUE1_LOW'				=> $sel_rogue1_low,
					'SEL_ROGUE1_MEDIUM'				=> $sel_rogue1_medium,
					'SEL_ROGUE1_HIGH'				=> $sel_rogue1_high,
					'SEL_ROGUE2_NONE'				=> $sel_rogue2_none,
					'SEL_ROGUE2_LOW'				=> $sel_rogue2_low,
					'SEL_ROGUE2_MEDIUM'				=> $sel_rogue2_medium,
					'SEL_ROGUE2_HIGH'				=> $sel_rogue2_high,
					'SEL_ROGUE3_NONE'				=> $sel_rogue3_none,
					'SEL_ROGUE3_LOW'				=> $sel_rogue3_low,
					'SEL_ROGUE3_MEDIUM'				=> $sel_rogue3_medium,
					'SEL_ROGUE3_HIGH'				=> $sel_rogue3_high,
					'SEL_ROGUE1_NUM'				=> $sel_rogue1_num,
					'SEL_ROGUE2_NUM'				=> $sel_rogue2_num,
					'SEL_ROGUE3_NUM'				=> $sel_rogue3_num,
					
					'SEL_SHAMAN1_NONE'				=> $sel_shaman1_none,
					'SEL_SHAMAN1_LOW'				=> $sel_shaman1_low,
					'SEL_SHAMAN1_MEDIUM'			=> $sel_shaman1_medium,
					'SEL_SHAMAN1_HIGH'				=> $sel_shaman1_high,
					'SEL_SHAMAN2_NONE'				=> $sel_shaman2_none,
					'SEL_SHAMAN2_LOW'				=> $sel_shaman2_low,
					'SEL_SHAMAN2_MEDIUM'			=> $sel_shaman2_medium,
					'SEL_SHAMAN2_HIGH'				=> $sel_shaman2_high,
					'SEL_SHAMAN3_NONE'				=> $sel_shaman3_none,
					'SEL_SHAMAN3_LOW'				=> $sel_shaman3_low,
					'SEL_SHAMAN3_MEDIUM'			=> $sel_shaman3_medium,
					'SEL_SHAMAN3_HIGH'				=> $sel_shaman3_high,
					'SEL_SHAMAN1_NUM'				=> $sel_shaman1_num,
					'SEL_SHAMAN2_NUM'				=> $sel_shaman2_num,
					'SEL_SHAMAN3_NUM'				=> $sel_shaman3_num,
					
					'SEL_WARLOCK1_NONE'				=> $sel_warlock1_none,
					'SEL_WARLOCK1_LOW'				=> $sel_warlock1_low,
					'SEL_WARLOCK1_MEDIUM'			=> $sel_warlock1_medium,
					'SEL_WARLOCK1_HIGH'				=> $sel_warlock1_high,
					'SEL_WARLOCK2_NONE'				=> $sel_warlock2_none,
					'SEL_WARLOCK2_LOW'				=> $sel_warlock2_low,
					'SEL_WARLOCK2_MEDIUM'			=> $sel_warlock2_medium,
					'SEL_WARLOCK2_HIGH'				=> $sel_warlock2_high,
					'SEL_WARLOCK3_NONE'				=> $sel_warlock3_none,
					'SEL_WARLOCK3_LOW'				=> $sel_warlock3_low,
					'SEL_WARLOCK3_MEDIUM'			=> $sel_warlock3_medium,
					'SEL_WARLOCK3_HIGH'				=> $sel_warlock3_high,
					'SEL_WARLOCK1_NUM'				=> $sel_warlock1_num,
					'SEL_WARLOCK2_NUM'				=> $sel_warlock2_num,
					'SEL_WARLOCK3_NUM'				=> $sel_warlock3_num,
					
					'SEL_WARRIOR1_NONE'				=> $sel_warrior1_none,
					'SEL_WARRIOR1_LOW'				=> $sel_warrior1_low,
					'SEL_WARRIOR1_MEDIUM'			=> $sel_warrior1_medium,
					'SEL_WARRIOR1_HIGH'				=> $sel_warrior1_high,
					'SEL_WARRIOR2_NONE'				=> $sel_warrior2_none,
					'SEL_WARRIOR2_LOW'				=> $sel_warrior2_low,
					'SEL_WARRIOR2_MEDIUM'			=> $sel_warrior2_medium,
					'SEL_WARRIOR2_HIGH'				=> $sel_warrior2_high,
					'SEL_WARRIOR3_NONE'				=> $sel_warrior3_none,
					'SEL_WARRIOR3_LOW'				=> $sel_warrior3_low,
					'SEL_WARRIOR3_MEDIUM'			=> $sel_warrior3_medium,
					'SEL_WARRIOR3_HIGH'				=> $sel_warrior3_high,
					'SEL_WARRIOR1_NUM'				=> $sel_warrior1_num,
					'SEL_WARRIOR2_NUM'				=> $sel_warrior2_num,
					'SEL_WARRIOR3_NUM'				=> $sel_warrior3_num,
					
					'SEL_MONK1_NONE'				=> $sel_monk1_none,
					'SEL_MONK1_LOW'				=> $sel_monk1_low,
					'SEL_MONK1_MEDIUM'			=> $sel_monk1_medium,
					'SEL_MONK1_HIGH'				=> $sel_monk1_high,
					'SEL_MONK2_NONE'				=> $sel_monk2_none,
					'SEL_MONK2_LOW'				=> $sel_monk2_low,
					'SEL_MONK2_MEDIUM'			=> $sel_monk2_medium,
					'SEL_MONK2_HIGH'				=> $sel_monk2_high,
					'SEL_MONK3_NONE'				=> $sel_monk3_none,
					'SEL_MONK3_LOW'				=> $sel_monk3_low,
					'SEL_MONK3_MEDIUM'			=> $sel_monk3_medium,
					'SEL_MONK3_HIGH'				=> $sel_monk3_high,
					'SEL_MONK1_NUM'				=> $sel_monk1_num,
					'SEL_MONK2_NUM'				=> $sel_monk2_num,
					'SEL_MONK3_NUM'				=> $sel_monk3_num,	

					'U_ACTION'					=> $this->u_action,
					));
					
				$this->page_title = 'ACP_RB_CLASSES_INFO';
				$this->tpl_name = 'acp_recruit_block_classes';
				return;
			break;
		}
	}
}
?>