<?php
/**
 * main menu block
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


$template->assign_vars(array(
	'S_DISPLAY_MAINMENU' 	=> true,

	'U_LISTMEMBERS'  	=> append_sid("{$phpbb_root_path}dkp.$phpEx", '&amp;page=standings'),
	'U_LISTITEMS'     	=> append_sid("{$phpbb_root_path}dkp.$phpEx", '&amp;page=listitems'),  
	'U_LISTITEMHIST'  	=> append_sid("{$phpbb_root_path}dkp.$phpEx", '&amp;page=listitems&amp;mode=history'),
	'U_LISTEVENTS'  	=> append_sid("{$phpbb_root_path}dkp.$phpEx", '&amp;page=listevents'),  
	'U_LISTRAIDS'   	=> append_sid("{$phpbb_root_path}dkp.$phpEx", '&amp;page=listraids'),  
	'U_BP'   			=> append_sid("{$phpbb_root_path}dkp.$phpEx", '&amp;page=bossprogress'), 
	'U_ROSTER'   		=> append_sid("{$phpbb_root_path}dkp.$phpEx", '&amp;page=roster'), 
	'U_STATS'   		=> append_sid("{$phpbb_root_path}dkp.$phpEx", '&amp;page=stats'), 
	'U_BBFAQ'   		=> append_sid("{$phpbb_root_path}faq.$phpEx", 'mode=bbcode'),
	'U_TERMS'      		=> append_sid("{$phpbb_root_path}ucp.$phpEx", 'mode=terms'),
	'U_PRIV'      		=> append_sid("{$phpbb_root_path}ucp.$phpEx", 'mode=privacy'),
));


?>