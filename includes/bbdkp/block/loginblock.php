<?php
/**
 * login block
 * 
 * @package bbDkp
 * @copyright 2009 bbdkp <https://github.com/bbDKP>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * 
 */

if (!defined('IN_PHPBB'))
{
   exit;
}
/**  begin login block ***/

// Assign specific vars
$s_display = true;
$template->assign_vars(array(
	'U_PORTAL'				=> append_sid("{$phpbb_root_path}portal.$phpEx"),
	'S_DISPLAY_FULL_LOGIN'	=> ($s_display) ? true : false,
	'S_AUTOLOGIN_ENABLED'	=> ($config['allow_autologin']) ? true : false,
	'S_LOGIN_ACTION'		=> append_sid("{$phpbb_root_path}ucp.$phpEx", 'mode=login'),
	'L_UM_LOG_ME_IN'		=> $user->lang['REMEMBERME'],
));
/**  end login block ***/

?>