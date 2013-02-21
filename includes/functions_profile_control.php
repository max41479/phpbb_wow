<?php
/**
*
* @package phpBB3
* @version $Id functions_profile_control.php 1.0.0 2009-11-21 23:29:18GMT mtrs $
* @version $Id$
* @copyrigh(c) 2009 mtrs
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/
/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit();
}

//redirect user to ucp profile page to fill required profile fields
function update_proofile_fields()
{
	global $config, $user, $auth, $db, $phpbb_root_path, $phpEx;
	
	$mode = request_var('mode', '');
	if ($user->data['is_registered'] && !$user->data['is_bot'] && !($user->page['page_name'] == 'adm/index.' . $phpEx) && !($user->page['page_name'] == 'ucp.' . $phpEx && $mode == 'profile_info') && !($user->page['page_name'] == 'ucp.'.$phpEx && $mode == 'logout') && !($user->page['page_name'] == 'ucp.' . $phpEx && $mode == 'login'))
	{
		if (($config['ucp_icq'] == PROFILE_REQUIRE_REGISTRATION && empty($user->data['user_icq']) && $auth->acl_get('u_ucp_icq')) || ($config['ucp_aim'] == PROFILE_REQUIRE_REGISTRATION && empty($user->data['user_aim']) && $auth->acl_get('u_ucp_aim')) || ($config['ucp_msnm'] == PROFILE_REQUIRE_REGISTRATION && empty($user->data['user_msnm']) && $auth->acl_get('u_ucp_msnm')) || ($config['ucp_yim'] == PROFILE_REQUIRE_REGISTRATION  && empty($user->data['user_yim']) && $auth->acl_get('u_ucp_yim')) || ($config['ucp_jabber'] == PROFILE_REQUIRE_REGISTRATION  && empty($user->data['user_jabber']) && $auth->acl_get('u_ucp_jabber')) || ($config['ucp_website'] == PROFILE_REQUIRE_REGISTRATION && empty($user->data['user_website']) && $auth->acl_get('u_ucp_website')) || ($config['ucp_location'] == PROFILE_REQUIRE_REGISTRATION  && empty($user->data['user_from']) && $auth->acl_get('u_ucp_location')) || ($config['ucp_occupation'] == PROFILE_REQUIRE_REGISTRATION && empty($user->data['user_occ']) && $auth->acl_get('u_ucp_occupation')) || ($config['ucp_interests'] == PROFILE_REQUIRE_REGISTRATION  && empty($user->data['user_interests']) && $auth->acl_get('u_ucp_interests')) || ($config['allow_birthdays'] && $config['ucp_reg_birthday'] == PROFILE_REQUIRE_REGISTRATION && $auth->acl_get('u_ucp_birthday') && (empty($user->data['user_birthday']) || $user->data['user_birthday'] == '0- 0-   0')))	
		{
			$user->add_lang('mods/info_acp_profile_control');
			//Redirect to UCP 
			meta_refresh(4, append_sid("{$phpbb_root_path}ucp.$phpEx", 'i=profile&amp;mode=profile_info'));
			$message = $user->lang['UCP_PROFILE_UPDATE'] . '<br />' . sprintf($user->lang['RETURN_UCP'], '<a href="' . append_sid("{$phpbb_root_path}ucp.$phpEx", 'i=profile&amp;mode=profile_info') . '">', '</a>');

			trigger_error($message, E_USER_WARNING);
		}
		else
		{
			//Set user data, thus user will not be controlled again
			$sql = 'UPDATE ' . USERS_TABLE . '
				SET user_profile_reminder = 1
				WHERE user_id = ' . $user->data['user_id'];
			$db->sql_query($sql);
		}
	}
	return;
}


//viewtopic template variables
function profile_fields_variables(&$postrow, $user_cache_poster)
{
	global  $config;
		
	if (!$config['pfcm_enable'])
	{
		return;
	}
	$profile_data  = array(
			'POSTER_FROM'	=> ($config['ucp_location']) ? $user_cache_poster['from'] : '',
			'U_WWW'			=> ($config['ucp_website']) ? $user_cache_poster['www'] : '',
			'U_ICQ'			=> ($config['ucp_icq']) ? $user_cache_poster['icq'] : '',
			'U_AIM'			=> ($config['ucp_aim']) ? $user_cache_poster['aim'] : '',
			'U_MSN'			=> ($config['ucp_msnm']) ? $user_cache_poster['msn'] : '',
			'U_YIM'			=> ($config['ucp_yim']) ? $user_cache_poster['yim'] : '',
			'U_JABBER'		=> ($config['ucp_jabber']) ? $user_cache_poster['jabber'] : '',
		);

	$postrow = array_merge($postrow, $profile_data);		

}

//Assign template variables in memberlist body
function memberlist_profile_fields($member)
{
	global  $config;
	
	if (!$config['pfcm_enable'])
	{
		return array();
	}
	
	$memberlist_data  = array(
		'OCCUPATION'	=> (!empty($member['user_occ']) && $config['ucp_occupation']) ? censor_text($member['user_occ']) : '',
		'INTERESTS'		=> (!empty($member['user_interests']) && $config['ucp_interests']) ? censor_text($member['user_interests']) : '',
	);		

	return $memberlist_data;
}

//Assign template variables in user profile
function user_profile_fields($data, $user_id)
{
	global $config, $auth, $phpbb_root_path, $phpEx;
	
	if (!$config['pfcm_enable'])
	{
		return array();
	}
	
	$user_profile_data  = array(
			'U_WWW'			=> (!empty($data['user_website']) && $config['ucp_website']) ? $data['user_website'] : '',
			'U_SHORT_WWW'	=> (!empty($data['user_website']) && $config['ucp_website']) ? ((strlen($data['user_website']) > 55) ? substr($data['user_website'], 0, 39) . ' ... ' . substr($data['user_website'], -10) : $data['user_website']) : '',
			'U_ICQ'			=> ($data['user_icq'] && $config['ucp_icq']) ? 'http://www.icq.com/people/webmsg.php?to=' . urlencode($data['user_icq']) : '',
			'U_AIM'			=> ($data['user_aim'] && $auth->acl_get('u_sendim') && $config['ucp_aim']) ? append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=contact&amp;action=aim&amp;u=' . $user_id) : '',
			'U_YIM'			=> ($data['user_yim'] && $config['ucp_yim']) ? 'http://edit.yahoo.com/config/send_webmesg?.target=' . urlencode($data['user_yim']) . '&amp;.src=pg' : '',
			'U_MSN'			=> ($data['user_msnm'] && $auth->acl_get('u_sendim') && $config['ucp_msnm']) ? append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=contact&amp;action=msnm&amp;u=' . $user_id) : '',
			'U_JABBER'		=> ($data['user_jabber'] && $auth->acl_get('u_sendim') && $config['ucp_jabber']) ? append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=contact&amp;action=jabber&amp;u=' . $user_id) : '',
			'LOCATION'		=> ($data['user_from'] && $config['ucp_location']) ? $data['user_from'] : '',

			'USER_ICQ'		=> ($config['ucp_icq']) ? $data['user_icq'] : '',
			'USER_AIM'		=> ($config['ucp_aim']) ? $data['user_aim'] : '',
			'USER_YIM'		=> ($config['ucp_yim']) ? $data['user_yim'] : '',
			'USER_MSN'		=> ($config['ucp_msnm']) ? $data['user_msnm'] : '',
			'USER_JABBER'	=> ($config['ucp_jabber']) ? $data['user_jabber'] : '',
	);		

	return $user_profile_data;	
}

//Assign template pm view body
function pm_view_profile_fields($user_info, $author_id)
{
	global  $config, $auth, $phpbb_root_path, $phpEx;
	
	if (!$config['pfcm_enable'])
	{
		return array();
	}	
	
	$pm_view_user_data  = array(
			'AUTHOR_FROM'	=> (!empty($user_info['user_from']) && $config['ucp_location']) ? $user_info['user_from'] : '',
			'U_WWW'			=> (!empty($user_info['user_website']) && $config['ucp_website']) ? $user_info['user_website'] : '',
			'U_ICQ'			=> ($user_info['user_icq'] && $config['ucp_icq']) ? 'http://www.icq.com/people/webmsg.php?to=' . urlencode($user_info['user_icq']) : '',
			'U_AIM'			=> ($user_info['user_aim'] && $auth->acl_get('u_sendim') && $config['ucp_aim']) ? append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=contact&amp;action=aim&amp;u=' . $author_id) : '',
			'U_YIM'			=> ($user_info['user_yim'] && $config['ucp_yim']) ? 'http://edit.yahoo.com/config/send_webmesg?.target=' . urlencode($user_info['user_yim']) . '&amp;.src=pg' : '',
			'U_MSN'			=> ($user_info['user_msnm'] && $auth->acl_get('u_sendim') && $config['ucp_msnm']) ? append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=contact&amp;action=msnm&amp;u=' . $author_id) : '',
			'U_JABBER'		=> ($user_info['user_jabber'] && $auth->acl_get('u_sendim') && $config['ucp_jabber']) ? append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=contact&amp;action=jabber&amp;u=' . $author_id) : '',
		);		


	return $pm_view_user_data;
}	

//user profile validate data array
function ucp_validate_data(&$validate_array)
{
	global  $config, $auth;
	
	if (!$config['pfcm_enable'])
	{
		return;
	}

	$validate_array_new = array(
			'icq'			=> array(
				array('string', true, 3, 15),
				array('match', (($config['ucp_icq'] == PROFILE_REQUIRE_REGISTRATION && $auth->acl_get('u_ucp_icq')) ? false : true), '#^[0-9]+$#i')),
			'aim'			=> array('string', (($config['ucp_aim'] == PROFILE_REQUIRE_REGISTRATION && $auth->acl_get('u_ucp_aim')) ? false : true), 3, 255),
			'msn'			=> array('string', (($config['ucp_msnm'] == PROFILE_REQUIRE_REGISTRATION && $auth->acl_get('u_ucp_msnm')) ? false : true), 5, 255),
			'jabber'		=> array(
				array('string', (($config['ucp_jabber'] == PROFILE_REQUIRE_REGISTRATION && $auth->acl_get('u_ucp_jabber')) ? false : true), 5, 255),
				array('jabber')),
			'yim'			=> array('string', (($config['ucp_yim'] == PROFILE_REQUIRE_REGISTRATION && $auth->acl_get('u_ucp_yim')) ? false : true), 5, 255),
			'website'		=> array(
				array('string', true, 12, 255),
				array('match', (($config['ucp_website'] == PROFILE_REQUIRE_REGISTRATION && $auth->acl_get('u_ucp_website')) ? false : true), '#^http[s]?://(.*?\.)*?[a-z0-9\-]+\.[a-z]{2,4}#i')),
			'location'		=> array('string', (($config['ucp_location'] == PROFILE_REQUIRE_REGISTRATION && $auth->acl_get('u_ucp_location')) ? false : true), 2, 100),
			'occupation'	=> array('string', (($config['ucp_occupation'] == PROFILE_REQUIRE_REGISTRATION && $auth->acl_get('u_ucp_occupation')) ? false : true), 2, 500),
			'interests'		=> array('string', (($config['ucp_interests'] == PROFILE_REQUIRE_REGISTRATION && $auth->acl_get('u_ucp_interests')) ? false : true), 2, 500),
		);
		if ($config['allow_birthdays'] && $config['ucp_reg_birthday'])
		{
			$validate_array_new = array_merge($validate_array_new, array(
				'bday_day'		=> array('num', true, 1, 31),
				'bday_month'	=> array('num', true, 1, 12),
				'bday_year'		=> array('num', true, 1901, gmdate('Y', time()) + 50),
				'user_birthday' => array('date', (($config['ucp_reg_birthday'] > PROFILE_FIELD_ENABLED && $auth->acl_get('u_ucp_birthday')) ? false : true)),
			));
		}

		//In case there is any other mod using validate array, we take diff key to obtain keys other than default ones
		$validate_array = array_merge($validate_array_new, array_diff_key($validate_array, $validate_array_new));
}

//user profile profile fields
function ucp_profile_fields()
{
	global $config, $auth;
	
	$ucp_profile_data = array(
				'S_USER_UCP_ICQ'			=> (($auth->acl_get('u_ucp_icq') && $config['ucp_icq']) || !$config['pfcm_enable']) ? true : false,
				'S_USER_UCP_AIM'			=> (($auth->acl_get('u_ucp_aim') && $config['ucp_aim']) || !$config['pfcm_enable']) ? true : false,
				'S_USER_UCP_MSNM'			=> (($auth->acl_get('u_ucp_msnm') && $config['ucp_msnm']) || !$config['pfcm_enable']) ? true : false,
				'S_USER_UCP_YIM'			=> (($auth->acl_get('u_ucp_yim') && $config['ucp_yim']) || !$config['pfcm_enable']) ? true : false,
				'S_USER_UCP_JABBER'			=> (($auth->acl_get('u_ucp_jabber') && $config['ucp_jabber']) || !$config['pfcm_enable']) ? true : false,
				'S_USER_UCP_WEBSITE'		=> (($auth->acl_get('u_ucp_website') && $config['ucp_website']) || !$config['pfcm_enable']) ? true : false,
				'S_USER_UCP_LOCATION'		=> (($auth->acl_get('u_ucp_location') && $config['ucp_location']) || !$config['pfcm_enable']) ? true : false,
				'S_USER_UCP_OCCUPATION'		=> (($auth->acl_get('u_ucp_occupation') && $config['ucp_occupation']) || !$config['pfcm_enable']) ? true : false,
				'S_USER_UCP_INTERESTS'		=> (($auth->acl_get('u_ucp_interests') && $config['ucp_interests']) || !$config['pfcm_enable']) ? true : false,
				'S_BIRTHDAYS_ENABLED'		=> ($config['allow_birthdays'] && ($auth->acl_get('u_ucp_birthday') || !$config['pfcm_enable'])) ? true : false,
				'S_PFCM_ENABLED'			=> ($config['pfcm_enable']) ? true : false,
				
				'UCP_ICQ_REQUIRE'			=> ($config['ucp_icq'] == PROFILE_REQUIRE_REGISTRATION && $config['pfcm_enable']) ? true : false,
				'UCP_AIM_REQUIRE'			=> ($config['ucp_aim'] == PROFILE_REQUIRE_REGISTRATION && $config['pfcm_enable']) ? true : false,
				'UCP_MSNM_REQUIRE'			=> ($config['ucp_msnm'] == PROFILE_REQUIRE_REGISTRATION && $config['pfcm_enable']) ? true : false,
				'UCP_YIM_REQUIRE'			=> ($config['ucp_yim'] == PROFILE_REQUIRE_REGISTRATION && $config['pfcm_enable']) ? true : false,
				'UCP_JABBER_REQUIRE'		=> ($config['ucp_jabber'] == PROFILE_REQUIRE_REGISTRATION && $config['pfcm_enable']) ? true : false,
				'UCP_WEBSITE_REQUIRE'		=> ($config['ucp_website'] == PROFILE_REQUIRE_REGISTRATION && $config['pfcm_enable']) ? true : false,
				'UCP_LOCATION_REQUIRE'		=> ($config['ucp_location'] == PROFILE_REQUIRE_REGISTRATION && $config['pfcm_enable']) ? true : false,
				'UCP_OCCUPATION_REQUIRE'	=> ($config['ucp_occupation'] == PROFILE_REQUIRE_REGISTRATION && $config['pfcm_enable']) ? true : false,
				'UCP_INTERESTS_REQUIRE'		=> ($config['ucp_interests'] == PROFILE_REQUIRE_REGISTRATION && $config['pfcm_enable']) ? true : false,
				'UCP_BIRTHDAY_REQUIRE'		=> ($config['ucp_reg_birthday'] == PROFILE_DISPLAY_REGISTRATION && $config['pfcm_enable']) ? true : false,

	);
	
	return $ucp_profile_data;
}

//ucp register new profile fields data array
function ucp_register_data(&$data)
{
	global $config;
	
	if (!$config['pfcm_enable'])
	{
		return;
	}
		
	$ucp_register_data = array(
			'icq'			=> request_var('icq', ''),
			'aim'			=> request_var('aim', ''),
			'msn'			=> request_var('msn', ''),
			'yim'			=> request_var('yim', ''),
			'jabber'		=> utf8_normalize_nfc(request_var('jabber', '', true)),
			'website'		=> request_var('website', ''),
			'occupation'	=> utf8_normalize_nfc(request_var('occupation', '', true)),
			'interests'		=> utf8_normalize_nfc(request_var('interests', '', true)),
			'location'		=> utf8_normalize_nfc(request_var('location', '', true)),
			'website'		=> utf8_normalize_nfc(request_var('website', '', true)),
			'bday_day'		=> request_var('bday_day', 0),
			'bday_month'	=> request_var('bday_month', 0),
			'bday_year'		=> request_var('bday_year', 0),
	);
	
	$data = array_merge($data, $ucp_register_data);	
	$data['user_birthday'] = sprintf('%2d-%2d-%4d', $data['bday_day'], $data['bday_month'], $data['bday_year']);
}

//ucp register new regisrations user_row array
function ucp_register_user_row(&$user_row, $data)
{
	global $config;
	
	if (!$config['pfcm_enable'])
	{
		return;
	}
		
	$ucp_register_user_row = array(
			'user_icq'				=> $data['icq'],
			'user_aim'				=> $data['aim'],
			'user_msnm'				=> $data['msn'],
			'user_yim'				=> $data['yim'],
			'user_jabber'			=> $data['jabber'],
			'user_website'			=> $data['website'],
			'user_from'				=> $data['location'],
			'user_occ'				=> $data['occupation'],
			'user_interests'		=> $data['interests'],
			'user_birthday'			=> $data['user_birthday'],
	);

	$user_row = array_merge($user_row, $ucp_register_user_row);
}

//ucp register template variables
function ucp_register_template($data)
{
	global $config;
	
	if (!$config['pfcm_enable'])
	{
		return array();
	}	
	
	$birthday = array();

	$ucp_register_template = array(
			'ICQ'			=> $data['icq'],
			'YIM'			=> $data['yim'],
			'AIM'			=> $data['aim'],
			'MSN'			=> $data['msn'],
			'JABBER'		=> $data['jabber'],
			'WEBSITE'		=> $data['website'],
			'LOCATION'		=> $data['location'],
			'OCCUPATION'	=> $data['occupation'],
			'INTERESTS'		=> $data['interests'],
					
			'S_USER_REG_ICQ'			=> ($config['ucp_icq'] > PROFILE_FIELD_ENABLED) ? true : false,
			'S_USER_REG_AIM'			=> ($config['ucp_aim'] > PROFILE_FIELD_ENABLED) ? true : false,
			'S_USER_REG_MSNM'			=> ($config['ucp_msnm'] > PROFILE_FIELD_ENABLED) ? true : false,
			'S_USER_REG_YIM'			=> ($config['ucp_yim'] > PROFILE_FIELD_ENABLED) ? true : false,
			'S_USER_REG_JABBER'			=> ($config['ucp_jabber'] > PROFILE_FIELD_ENABLED) ? true : false,
			'S_USER_REG_WEBSITE'		=> ($config['ucp_website'] > PROFILE_FIELD_ENABLED) ? true : false,
			'S_USER_REG_LOCATION'		=> ($config['ucp_location'] > PROFILE_FIELD_ENABLED) ? true : false,
			'S_USER_REG_OCCUPATION'		=> ($config['ucp_occupation'] > PROFILE_FIELD_ENABLED) ? true : false,
			'S_USER_REG_INTERESTS'		=> ($config['ucp_interests'] > PROFILE_FIELD_ENABLED) ? true : false,	
			'S_USER_BIRTHDAY'			=> ($config['ucp_reg_birthday'] != PROFILE_FIELD_DISABLED && $config['allow_birthdays']) ? true : false,
			'S_PFCM_ENABLED'			=> ($config['pfcm_enable']) ? true : false,
			'S_PROFILE_OPTIONS'			=> (($config['allow_birthdays'] && $config['ucp_reg_birthday'] != PROFILE_FIELD_DISABLED) || $config['ucp_interests'] > PROFILE_FIELD_ENABLED || $config['ucp_interests'] > PROFILE_FIELD_ENABLED || $config['ucp_occupation'] > PROFILE_FIELD_ENABLED || $config['ucp_location'] > PROFILE_FIELD_ENABLED || $config['ucp_website'] > PROFILE_FIELD_ENABLED || $config['ucp_jabber'] > PROFILE_FIELD_ENABLED || $config['ucp_yim'] > PROFILE_FIELD_ENABLED || $config['ucp_msnm'] > PROFILE_FIELD_ENABLED || $config['ucp_aim'] > PROFILE_FIELD_ENABLED || $config['ucp_icq'] > PROFILE_FIELD_ENABLED) ? true : false,

			'ICQ_REQUIRE'			=> ($config['ucp_icq'] == PROFILE_REQUIRE_REGISTRATION) ? true : false,
			'AIM_REQUIRE'			=> ($config['ucp_aim'] == PROFILE_REQUIRE_REGISTRATION) ? true : false,
			'MSNM_REQUIRE'			=> ($config['ucp_msnm'] == PROFILE_REQUIRE_REGISTRATION) ? true : false,
			'YIM_REQUIRE'			=> ($config['ucp_yim'] == PROFILE_REQUIRE_REGISTRATION) ? true : false,
			'JABBER_REQUIRE'		=> ($config['ucp_jabber'] == PROFILE_REQUIRE_REGISTRATION) ? true : false,
			'WEBSITE_REQUIRE'		=> ($config['ucp_website'] == PROFILE_REQUIRE_REGISTRATION) ? true : false,
			'LOCATION_REQUIRE'		=> ($config['ucp_location'] == PROFILE_REQUIRE_REGISTRATION) ? true : false,
			'OCCUPATION_REQUIRE'	=> ($config['ucp_occupation'] == PROFILE_REQUIRE_REGISTRATION) ? true : false,
			'INTERESTS_REQUIRE'		=> ($config['ucp_interests'] == PROFILE_REQUIRE_REGISTRATION) ? true : false,
			'BIRTHDAY_REQUIRE'		=> ($config['ucp_reg_birthday'] == PROFILE_DISPLAY_REGISTRATION) ? true : false,	
	);
	
	if ($config['allow_birthdays'] && $config['ucp_reg_birthday'] != PROFILE_FIELD_DISABLED)
	{
		$s_birthday_day_options = '<option value="0"' . ((!$data['bday_day']) ? ' selected="selected"' : '') . '>--</option>';
		for ($i = 1; $i < 32; $i++)
		{
			$selected = ($i == $data['bday_day']) ? ' selected="selected"' : '';
			$s_birthday_day_options .= "<option value=\"$i\"$selected>$i</option>";
		}

		$s_birthday_month_options = '<option value="0"' . ((!$data['bday_month']) ? ' selected="selected"' : '') . '>--</option>';
		for ($i = 1; $i < 13; $i++)
		{
			$selected = ($i == $data['bday_month']) ? ' selected="selected"' : '';
			$s_birthday_month_options .= "<option value=\"$i\"$selected>$i</option>";
		}
		$s_birthday_year_options = '';

		$now = getdate();
		$s_birthday_year_options = '<option value="0"' . ((!$data['bday_year']) ? ' selected="selected"' : '') . '>--</option>';
		for ($i = $now['year'] - 100; $i <= $now['year']; $i++)
		{
			$selected = ($i == $data['bday_year']) ? ' selected="selected"' : '';
			$s_birthday_year_options .= "<option value=\"$i\"$selected>$i</option>";
		}
		unset($now);

		$birthday = array(
				'S_BIRTHDAY_DAY_OPTIONS'	=> $s_birthday_day_options,
				'S_BIRTHDAY_MONTH_OPTIONS'	=> $s_birthday_month_options,
				'S_BIRTHDAY_YEAR_OPTIONS'	=> $s_birthday_year_options,
			);
	}
	$ucp_register_template = array_merge($ucp_register_template, $birthday);
	
	return $ucp_register_template;
	
}

//user_add function user_row data of new fields
function register_user_function(&$sql_ary, $user_row)
{
	global $config;
	
	if (!$config['pfcm_enable'])
	{
		return array();
	}	
	
	$register_user_function = array(
			'user_icq'			=> (isset($user_row['user_icq'])) ? $user_row['user_icq'] : '',
			'user_aim'			=> (isset($user_row['user_aim'])) ? $user_row['user_aim'] : '',
			'user_msnm'			=> (isset($user_row['user_msnm'])) ? $user_row['user_msnm'] : '',
			'user_yim'			=> (isset($user_row['user_yim'])) ? $user_row['user_yim'] : '',
			'user_jabber'		=> (isset($user_row['user_jabber'])) ? $user_row['user_jabber'] : '',		
			'user_website'		=> (isset($user_row['user_website'])) ? $user_row['user_website'] : '',
			'user_from'			=> (isset($user_row['user_from'])) ? $user_row['user_from'] : '',
			'user_occ'			=> (isset($user_row['user_occ'])) ? $user_row['user_occ'] : '',
			'user_interests'	=> (isset($user_row['user_interests'])) ? $user_row['user_interests'] : '',
			'user_birthday'		=> (isset($user_row['user_birthday'])) ? $user_row['user_birthday'] : '',
	);	
	
	$sql_ary = array_merge($sql_ary, $register_user_function);
	
}

function validate_profile_fields_register()
{
	global $config;
		
	if (!$config['pfcm_enable'])
	{
		return array();
	}
	
	$validate_data = array(
				'icq'			=> array(
					array('string', true, 3, 15),
					array('match', (($config['ucp_icq'] == PROFILE_REQUIRE_REGISTRATION) ? false : true), '#^[0-9]+$#i')),
				'aim'			=> array('string', (($config['ucp_aim'] == PROFILE_REQUIRE_REGISTRATION) ? false : true), 3, 255),
				'msn'			=> array('string', (($config['ucp_msnm'] == PROFILE_REQUIRE_REGISTRATION) ? false : true), 5, 255),
				'jabber'		=> array(
					array('string', (($config['ucp_jabber'] == PROFILE_REQUIRE_REGISTRATION) ? false : true), 5, 255),
					array('jabber')),
				'yim'			=> array('string', (($config['ucp_yim'] == PROFILE_REQUIRE_REGISTRATION) ? false : true), 5, 255),
				'website'		=> array(
					array('string', true, 12, 255),
					array('match', (($config['ucp_website'] == PROFILE_REQUIRE_REGISTRATION) ? false : true), '#^http[s]?://(.*?\.)*?[a-z0-9\-]+\.[a-z]{2,4}#i')),
				'location'		=> array('string', (($config['ucp_location'] == PROFILE_REQUIRE_REGISTRATION) ? false : true), 2, 100),
				'occupation'	=> array('string', (($config['ucp_occupation'] == PROFILE_REQUIRE_REGISTRATION) ? false : true), 2, 500),
				'interests'		=> array('string', (($config['ucp_interests'] == PROFILE_REQUIRE_REGISTRATION) ? false : true), 2, 500),	
				'bday_day'		=> array('num', true, 1, 31),
				'bday_month'	=> array('num', true, 1, 12),
				'bday_year'		=> array('num', true, 1901, gmdate('Y', time()) + 50),
				'user_birthday' => array('date', (($config['ucp_reg_birthday'] > PROFILE_FIELD_ENABLED && $config['allow_birthdays']) ? false : true)),						
		);
			
	return $validate_data;
}

?>