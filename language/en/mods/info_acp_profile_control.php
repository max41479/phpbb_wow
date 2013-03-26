<?php
/**
*
* 
* @package language
* @version $Id: acp_profile_control.php,v1.0.0 2009/11/21 12:53:34  Exp $
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
		'ACP_PROFILE_CONTROL'					=> 'Profile fields',
		'ACP_PROFILE_CONTROL_EXPLAIN'			=> 'You can activate and deactivate default profile fields in this panel. You can also display profile fields on registration, and require on registration and user control panel. If a profile field is required, then it is also displayed on registration screen.',
		'ACP_PROFILE_FIELDS'					=> 'Profile fields control',
		'PFCM_ENABLE'							=> 'Enable profile fields control MOD',
		'PFCM_ENABLE_EXPLAIN'					=> 'Disabling mod, makes board use standard features of profile fields.',
		'REQUIRED'								=> 'Required',
		'PROFILE_UCP_REGS'						=> 'Display on registration',		
		'ACTIVATE_FIRST'						=> 'Activate profile field first',
		
		'PROFILE_FIELD_NAME'					=> 'Profile field name',
		'PROFILE_FIELD_NAME_EXPLAIN'			=> 'In this screen you set default profile fields, displayed on registration, and required on registration and user control panel. Please not that, if you require a profile field, it will be also displayed on registration. However, you can hide profile fields at user control panel by changing user permsissions.',
		'DISPLAY_ON_PROFILE'					=> 'Display on profile',
		'DISPLAY_ON_PROFILE_EXPLAIN'			=> 'This is the default setting, the user can only see this profile field within the user control panel.',
		'DISPLAY_ON_REGISTRATION'				=> 'Display on registration screen',
		'DISPLAY_ON_REGISTRATION_EXPLAIN'		=> 'If this option is enabled, the field will be displayed on registration.',
		'REQUIRED_PROFILE_FIELD'				=> 'Required field and also display on regisration',
		'REQUIRED_PROFILE_FIELD_EXPLAIN'		=> 'Force profile field to be filled out or specified by user or administrator. If a profile field is required, it will be also displayed on registration screen.',

		'PROFILE_UPDATE_ENABLE'					=> 'Remind updating required profile fields',
		'PROFILE_UPDATE_ENABLE_EXPLAIN'			=> 'When enabled, redirects users to update required and not completed profile fields.',
		'PROFILE_UPDATE_REMINDER'				=> 'Remind profile fields updates',
		'PROFILE_UPDATE_REMINDER_EXPLAIN'		=> 'Resetting reminder redirects users to complete required profile fields.',
		'RESET_PROFILE_REMINDER'				=> 'Profile fields reminder reset',
		'UCP_PROFILE_UPDATE'					=> 'Please update your profile fields.',
		'RETURN_UCP'							=> '<br />%sClick to go to the User Control Panel%s',
));

?>