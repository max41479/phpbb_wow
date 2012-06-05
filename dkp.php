<?php
/**
 * @package bbDKP
 * @copyright (c) 2010 bbDKP (http://www.bbdkp.com)
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 1.2.3
 * 
 */
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');
$user->add_lang(array('mods/dkp_common', 'mods/dkp_admin'));

// check if user can access pages
if (! defined ( "EMED_BBDKP" ))
{
	trigger_error ( $user->lang['BBDKPDISABLED'] , E_USER_WARNING );
}

if (!$auth->acl_get('u_dkp'))
{
	trigger_error('NOT_AUTHORISED');
}

$template->assign_vars(array(
	'U_NEWS'  			=> append_sid("{$phpbb_root_path}dkp.$phpEx", 'page=news'),
	'U_LISTMEMBERS'  	=> append_sid("{$phpbb_root_path}dkp.$phpEx", 'page=standings'),
	'U_LISTITEMS'     	=> append_sid("{$phpbb_root_path}dkp.$phpEx", 'page=listitems'),  
	'U_LISTITEMHIST'  	=> append_sid("{$phpbb_root_path}dkp.$phpEx", 'page=listitems&amp;mode=history'),
	'U_LISTEVENTS'  	=> append_sid("{$phpbb_root_path}dkp.$phpEx", 'page=listevents'),  
	'U_LISTRAIDS'   	=> append_sid("{$phpbb_root_path}dkp.$phpEx", 'page=listraids'),  
	'U_VIEWITEM'   		=> append_sid("{$phpbb_root_path}dkp.$phpEx", 'page=viewitem'), 
	'U_VIEWMEMBER'   	=> append_sid("{$phpbb_root_path}dkp.$phpEx", 'page=viewmember'), 
	'U_VIEWRAID'   		=> append_sid("{$phpbb_root_path}dkp.$phpEx", 'page=viewraid'), 
	'U_BP'   			=> append_sid("{$phpbb_root_path}dkp.$phpEx", 'page=bossprogress'), 
	'U_ROSTER'   		=> append_sid("{$phpbb_root_path}dkp.$phpEx", 'page=roster'), 
	'U_STATS'   		=> append_sid("{$phpbb_root_path}dkp.$phpEx", 'page=stats'), 
	'U_ABOUT'         	=> append_sid("{$phpbb_root_path}aboutbbdkp.$phpEx"),
	'U_DKP_ACP'			=> ($auth->acl_get('a_') && !empty($user->data['is_registered'])) ? append_sid("{$phpbb_root_path}adm/index.$phpEx", 'i=' . (isset($config['bbdkp_module_id']) ? $config['bbdkp_module_id'] : 194) ,true,$user->session_id ) :'',
));	

$page =  request_var('page', 'standings');

// if bbTips exists then load it
$bbDKP_Admin = new bbDKP_Admin();
if ($bbDKP_Admin->bbtips == true)
{
	if (! class_exists ( 'bbtips' ))
	{
		require ($phpbb_root_path . 'includes/bbdkp/bbtips/parse.' . $phpEx);
	}
	$bbtips = new bbtips ( );
}

define('IN_BBDKP', true);
 
// load modules
switch ($page)
{
	case 'news':
		page_header($user->lang['MENU_NEWS']);
		include($phpbb_root_path . 'includes/bbdkp/module/news.' . $phpEx);
		break;
	case 'standings':
		page_header($user->lang['MENU_STANDINGS']);
		include($phpbb_root_path . 'includes/bbdkp/module/standings.' . $phpEx);
		break;
	case 'listitems':
		include($phpbb_root_path . 'includes/bbdkp/module/listitems.' . $phpEx);
		break;
	case 'listevents':
		include($phpbb_root_path . 'includes/bbdkp/module/listevents.' . $phpEx);
		break;
	case 'stats':
		include($phpbb_root_path . 'includes/bbdkp/module/stats.' . $phpEx);
		break;
	case 'listraids':
		include($phpbb_root_path . 'includes/bbdkp/module/listraids.' . $phpEx);		
		break;
	case 'viewevent':
		include($phpbb_root_path . 'includes/bbdkp/module/viewevent.' . $phpEx);
		break;
	case 'viewitem':
		include($phpbb_root_path . 'includes/bbdkp/module/viewitem.' . $phpEx);
		break;
	case 'viewraid':
		include($phpbb_root_path . 'includes/bbdkp/module/viewraid.' . $phpEx);
		break;
	case 'viewmember':
		include($phpbb_root_path . 'includes/bbdkp/module/viewmember.' . $phpEx);
		break;
	case 'bossprogress':
		include($phpbb_root_path . 'includes/bbdkp/module/bossprogress.' . $phpEx);
		break;		
	case 'roster':
		include($phpbb_root_path . 'includes/bbdkp/module/roster.' . $phpEx);
		break;	
	case 'planner':
		include($phpbb_root_path . 'includes/bbdkp/raidplanner/planner.' . $phpEx);
		break;	
	case 'planneradd':
		include($phpbb_root_path . 'includes/bbdkp/raidplanner/planneradd.' . $phpEx);
		break;		
}
$template->set_filenames(array(
	'body' => 'dkp/dkpmain.html')
);

page_footer();

?>