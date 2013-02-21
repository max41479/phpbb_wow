<?php
/**
*
* @package acp
* @version $Id: acp_words.php 8479 2008-03-29 00:22:48Z naderman $
* @version $Id: acp_profile_control.php, v1.0.0 2009-11-21 - modified by mtrs for profile control ACP module
* @copyright (c) 2005 phpBB Group
* @copyright (c) 2009 mtrs
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

class acp_profile_control
{
	var $u_action;
	
	function main($id, $mode)
	{
		global $db, $user, $config, $template, $phpbb_root_path, $phpEx;
		$user->add_lang(array('ucp', 'acp/profile'));

		// Set up general vars
		$action = request_var('action', '');
		$action = (request_var('add', '')) ? 'add' : ((request_var('save', '')) ? 'save' : $action);
		$action = (request_var('reminder', '')) ? 'reminder' : $action;
		$action = (request_var('reminder-reset', '')) ? 'reminder-reset' : $action;		

		$this->tpl_name = 'acp_profile_fields';
		$this->page_title = 'ACP_PROFILE_FIELDS';

		$s_hidden_fields = '';
		$form_name = 'acp_profile_fields';
		add_form_key($form_name);

		switch ($action)
		{
			case 'edit':

				$field_name = request_var('field_name', '');
				$config_name = ($field_name == 'birthday' ) ? 'ucp_reg_' . $field_name : 'ucp_' . $field_name;
				
				$template->assign_vars(array(
					'S_EDIT_PROFILE'	=> true,				
					'U_ACTION'			=> $this->u_action,
					'U_BACK'			=> $this->u_action,					
					'S_SHOW_ON_REG'		=> ($field_name != 'birthday') ? (($config[$config_name] >= 2) ? true : false) : (($config[$config_name] >= 1) ? true : false),
					'S_FIELD_REQUIRED'	=> ($field_name != 'birthday') ? (($config[$config_name] == 3) ? true : false) :  (($config[$config_name] == 2) ? true : false),
					'S_FIELD_NAME'		=> ($field_name != 'birthday') ? (isset($user->lang[strtoupper('UCP_' . $field_name)]) ? $user->lang[strtoupper('UCP_' . $field_name)] : $user->lang[strtoupper($field_name)]) : $user->lang['BIRTHDAY'],
					'S_CONFIG_NAME'		=> $config_name,
					'S_HIDDEN_FIELDS'	=> $s_hidden_fields
				));			
				
			break;					

			case 'save':
				
				if (!check_form_key($form_name))
				{
					trigger_error($user->lang['FORM_INVALID']. adm_back_link($this->u_action), E_USER_WARNING);
				}
				
				$config_name = request_var('config_name', '');
				$field_option = request_var('field_option', 0);
	
				$config_value = ($config_name == 'ucp_reg_birthday') ? ($field_option - 1) : $field_option;
				set_config($config_name, $config_value);

				trigger_error($user->lang['CHANGED_PROFILE_FIELD'] . adm_back_link($this->u_action));

			break;
	
			case 'reminder':
				
				//Activate profile fields updater and enable mod
				$ucp_prof_rem_enable = request_var('ucp_prof_rem_enable', 0);
				$pfcm_enable = request_var('pfcm_enable', 0);
		
				set_config('pfcm_enable', $pfcm_enable);
				set_config('ucp_prof_rem_enable', $ucp_prof_rem_enable);
				
				if ($ucp_prof_rem_enable != $config['ucp_prof_rem_enable'])
				{
					add_log('admin', 'RESET_PROFILE_REMINDER');
				}
				trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));

			break;	
	
			case 'reminder-reset':
				
				//Remind users to update required profile fields
				$sql = 'UPDATE ' . USERS_TABLE . '
					SET user_profile_reminder = 0
					WHERE user_profile_reminder = 1
						AND user_type <> ' . USER_IGNORE;
				$db->sql_query($sql);
				
				add_log('admin', 'RESET_PROFILE_REMINDER');			
				trigger_error($user->lang['RESET_PROFILE_REMINDER'] . adm_back_link($this->u_action));

			break;		
			
			case 'activate':
				
				//Activate config fields
				$field_name = request_var('field_name', '');
		
				if (empty($field_name))
				{
					trigger_error($user->lang['NO_TOPIC'] . adm_back_link($this->u_action), E_USER_WARNING);
				}		

				$field_name = ($field_name == 'birthday') ? 'allow_birthdays' : 'ucp_' . $field_name;
				
				set_config($field_name, 1);
				trigger_error($user->lang['PROFILE_FIELD_ACTIVATED'] . adm_back_link($this->u_action));
				
			break;
			
			case 'deactivate':

				//Deactivate config fields
				$field_name = request_var('field_name', '');
				
				if (empty($field_name))
				{
					trigger_error($user->lang['NO_TOPIC'] . adm_back_link($this->u_action), E_USER_WARNING);
				}

				$field_name = ($field_name == 'birthday') ? 'allow_birthdays' : 'ucp_' . $field_name;

				set_config($field_name, 0);
				trigger_error($user->lang['PROFILE_FIELD_DEACTIVATED'] . adm_back_link($this->u_action));

			break;			
		}

		$activate = $user->lang['ACTIVATE'];
		$deactivate = $user->lang['DEACTIVATE'];
		
		$template->assign_vars( array(
			'U_ACTION'							=> $this->u_action,
			'L_ICQ_ACTIVATE_DEACTIVATE'			=> (!$config['ucp_icq']) ? $activate : $deactivate,
			'U_ICQ_ACTIVATE_DEACTIVATE'			=> (!$config['ucp_icq']) ? $this->u_action . "&amp;action=activate&amp;field_name=icq" : $this->u_action . "&amp;action=deactivate&amp;field_name=icq",
			'U_EDIT_ICQ'						=> $this->u_action . "&amp;action=edit&amp;field_name=icq",
			'S_ICQ_ACTIVE'						=> ($config['ucp_icq']) ? true : false,
			'S_ICQ_AT_REGISTER'					=> ($config['ucp_icq'] >= 2) ? true : false,
			'S_ICQ_REQUIRED'					=> ($config['ucp_icq'] == 3) ? true : false,			
			
			'L_AIM_ACTIVATE_DEACTIVATE'			=> (!$config['ucp_aim']) ? $activate : $deactivate,
			'U_AIM_ACTIVATE_DEACTIVATE'			=> (!$config['ucp_aim']) ? $this->u_action . "&amp;action=activate&amp;field_name=aim" : $this->u_action . "&amp;action=deactivate&amp;field_name=aim",		
			'U_EDIT_AIM'						=> $this->u_action . "&amp;action=edit&amp;field_name=aim",
			'S_AIM_ACTIVE'						=> ($config['ucp_aim']) ? true : false,
			'S_AIM_AT_REGISTER'					=> ($config['ucp_aim'] >= 2) ? true : false,
			'S_AIM_REQUIRED'					=> ($config['ucp_aim'] == 3) ? true : false,	
			
			'L_MSNM_ACTIVATE_DEACTIVATE'		=> (!$config['ucp_msnm']) ? $activate : $deactivate,
			'U_MSNM_ACTIVATE_DEACTIVATE'		=> (!$config['ucp_msnm']) ? $this->u_action . "&amp;action=activate&amp;field_name=msnm" : $this->u_action . "&amp;action=deactivate&amp;field_name=msnm",		
			'U_EDIT_MSNM'						=> $this->u_action . "&amp;action=edit&amp;field_name=msnm",
			'S_MSNM_ACTIVE'						=> ($config['ucp_msnm']) ? true : false,
			'S_MSNM_AT_REGISTER'				=> ($config['ucp_msnm'] >= 2) ? true : false,
			'S_MSNM_REQUIRED'					=> ($config['ucp_msnm'] == 3) ? true : false,	
			
			'L_YIM_ACTIVATE_DEACTIVATE'			=> (!$config['ucp_yim']) ? $activate : $deactivate,
			'U_YIM_ACTIVATE_DEACTIVATE'			=> (!$config['ucp_yim']) ? $this->u_action . "&amp;action=activate&amp;field_name=yim" : $this->u_action . "&amp;action=deactivate&amp;field_name=yim",		
			'U_EDIT_YIM'						=> $this->u_action . "&amp;action=edit&amp;field_name=yim",
			'S_YIM_ACTIVE'						=> ($config['ucp_yim']) ? true : false,
			'S_YIM_AT_REGISTER'					=> ($config['ucp_yim'] >= 2) ? true : false,
			'S_YIM_REQUIRED'					=> ($config['ucp_yim'] == 3) ? true : false,	
			
			'L_JABBER_ACTIVATE_DEACTIVATE'		=> (!$config['ucp_jabber']) ? $activate : $deactivate,
			'U_JABBER_ACTIVATE_DEACTIVATE'		=> (!$config['ucp_jabber']) ? $this->u_action . "&amp;action=activate&amp;field_name=jabber" : $this->u_action . "&amp;action=deactivate&amp;field_name=jabber",		
			'U_EDIT_JABBER'						=> $this->u_action . "&amp;action=edit&amp;field_name=jabber",
			'S_JABBER_ACTIVE'					=> ($config['ucp_jabber']) ? true : false,
			'S_JABBER_AT_REGISTER'				=> ($config['ucp_jabber'] >= 2) ? true : false,
			'S_JABBER_REQUIRED'					=> ($config['ucp_jabber'] == 3) ? true : false,	
			
			'L_WEBSITE_ACTIVATE_DEACTIVATE'		=> (!$config['ucp_website']) ? $activate : $deactivate,
			'U_WEBSITE_ACTIVATE_DEACTIVATE'		=> (!$config['ucp_website']) ? $this->u_action . "&amp;action=activate&amp;field_name=website" : $this->u_action . "&amp;action=deactivate&amp;field_name=website",		
			'U_EDIT_WEBSITE'					=> $this->u_action . "&amp;action=edit&amp;field_name=website",
			'S_WEBSITE_ACTIVE'					=> ($config['ucp_website']) ? true : false,
			'S_WEBSITE_AT_REGISTER'				=> ($config['ucp_website'] >= 2) ? true : false,
			'S_WEBSITE_REQUIRED'				=> ($config['ucp_website'] == 3) ? true : false,
			
			'L_LOCATION_ACTIVATE_DEACTIVATE'	=> (!$config['ucp_location']) ? $activate : $deactivate,
			'U_LOCATION_ACTIVATE_DEACTIVATE'	=> (!$config['ucp_location']) ? $this->u_action . "&amp;action=activate&amp;field_name=location" : $this->u_action . "&amp;action=deactivate&amp;field_name=location",		
			'U_EDIT_LOCATION'					=> $this->u_action . "&amp;action=edit&amp;field_name=location",
			'S_LOCATION_ACTIVE'					=> ($config['ucp_location']) ? true : false,
			'S_LOCATION_AT_REGISTER'			=> ($config['ucp_location'] >= 2) ? true : false,
			'S_LOCATION_REQUIRED'				=> ($config['ucp_location'] == 3) ? true : false,	
			
			'L_OCCUPATION_ACTIVATE_DEACTIVATE'	=> (!$config['ucp_occupation']) ? $activate : $deactivate,
			'U_OCCUPATION_ACTIVATE_DEACTIVATE'	=> (!$config['ucp_occupation']) ? $this->u_action . "&amp;action=activate&amp;field_name=occupation" : $this->u_action . "&amp;action=deactivate&amp;field_name=occupation",		
			'U_EDIT_OCCUPATION'					=> $this->u_action . "&amp;action=edit&amp;field_name=occupation",
			'S_OCCUPATION_ACTIVE'				=> ($config['ucp_occupation']) ? true : false,
			'S_OCCUPATION_AT_REGISTER'			=> ($config['ucp_occupation'] >= 2) ? true : false,
			'S_OCCUPATION_REQUIRED'				=> ($config['ucp_occupation'] == 3) ? true : false,	
			
			'L_INTERESTS_ACTIVATE_DEACTIVATE'	=> (!$config['ucp_interests']) ? $activate : $deactivate,
			'U_INTERESTS_ACTIVATE_DEACTIVATE'	=> (!$config['ucp_interests']) ? $this->u_action . "&amp;action=activate&amp;field_name=interests" : $this->u_action . "&amp;action=deactivate&amp;field_name=interests",		
			'U_EDIT_INTERESTS'					=> $this->u_action . "&amp;action=edit&amp;field_name=interests",
			'S_INTERESTS_ACTIVE'				=> ($config['ucp_interests']) ? true : false,
			'S_INTERESTS_AT_REGISTER'			=> ($config['ucp_interests'] >= 2) ? true : false,
			'S_INTERESTS_REQUIRED'				=> ($config['ucp_interests'] == 3) ? true : false,	
			
			'L_BIRTHDAY_ACTIVATE_DEACTIVATE'	=> (!$config['allow_birthdays']) ? $activate : $deactivate,
			'U_BIRTHDAY_ACTIVATE_DEACTIVATE'	=> (!$config['allow_birthdays']) ? $this->u_action . "&amp;action=activate&amp;field_name=birthday" : $this->u_action . "&amp;action=deactivate&amp;field_name=birthday",		
			'U_EDIT_BIRTHDAY'					=> $this->u_action . "&amp;action=edit&amp;field_name=birthday",
			'S_BIRTHDAY_ACTIVE'					=> ($config['allow_birthdays']) ? true : false,
			'S_BIRTHDAY_AT_REGISTER'			=> ($config['allow_birthdays'] && $config['ucp_reg_birthday'] >= 1) ? true : false,
			'S_BIRTHDAY_REQUIRED'				=> ($config['allow_birthdays'] && $config['ucp_reg_birthday'] == 2) ? true : false,	
			
			'EDIT_FIELD'						=> $phpbb_root_path . 'adm/images/icon_edit.gif',
			'S_UPDATE_ENABLE'					=> ($config['ucp_prof_rem_enable']) ? true : false,
			'S_PFCM_ENABLE'						=> ($config['pfcm_enable']) ? true : false,

		));
	}
}

?>