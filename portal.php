<?php
/**
 * Indexpage for bbdkp
 * 
 * @package bbDKP
 * @copyright 2011 bbdkp <https://github.com/bbDKP>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author ippehe <ippe.he@gmail.com>

 * 
 */

/**
* @ignore
*/
define('IN_PHPBB', true);
define('IN_BBDKP', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

if(!defined("EMED_BBDKP"))
{
    trigger_error($user->lang['BBDKPDISABLED'], E_USER_WARNING); 
    
}
if (!isset($config['bbdkp_version']))
{
	// THE CONFIGS AND DATABASE TABLES AREN'T INSTALLED, EXIT
    trigger_error('GENERAL_ERROR', E_USER_WARNING); 
    
}
// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');
$user->add_lang(array('mods/dkp_common'));
/***** blocks ************/

/* fixed bocks -- always displayed */
include($phpbb_root_path . 'includes/bbdkp/block/newsblock.' . $phpEx);

if ($config['bbdkp_portal_rtshow'] == 1 )
{
	include($phpbb_root_path . 'includes/bbdkp/block/recentblock.' . $phpEx);
}

/* show loginbox or usermenu */
if ($user->data['is_registered'])
{
	include($phpbb_root_path .'includes/bbdkp/block/userblock.' . $phpEx);
}
else
{
	include($phpbb_root_path . 'includes/bbdkp/block/loginblock.' . $phpEx);
}

include($phpbb_root_path . 'includes/bbdkp/block/whoisonline.' . $phpEx);

// variable blocks - these depend on acp
if ($config['bbdkp_portal_newmembers'] == 1)
{
	include($phpbb_root_path . 'includes/bbdkp/block/newmembers.' . $phpEx);
}

if ($config['bbdkp_portal_welcomemsg'] == 1)
{
	include($phpbb_root_path . 'includes/bbdkp/block/welcomeblock.' . $phpEx);
}

if ($config['bbdkp_portal_menu'] == 1)
{
	include($phpbb_root_path . 'includes/bbdkp/block/mainmenublock.' . $phpEx);
}

if ($config['bbdkp_portal_loot'] == 1 )
{
	include($phpbb_root_path . 'includes/bbdkp/block/lootblock.' . $phpEx);
}

if ($config['bbdkp_portal_recruitment'] == 1)
{
	include($phpbb_root_path . 'includes/bbdkp/block/recruitmentblock.' . $phpEx);
}

$template->assign_var('S_BPSHOW', false);
if (isset($config['bbdkp_bp_version']))
{
	if ($config['bbdkp_portal_bossprogress'] == 1)
	{
		include($phpbb_root_path . 'includes/bbdkp/block/bossprogressblock.' . $phpEx);
		$template->assign_var('S_BPSHOW', true);
	}
}

if ($config['bbdkp_portal_links'] == 1)
{
	include($phpbb_root_path . 'includes/bbdkp/block/linksblock.' . $phpEx);
}
// Recruitment Block - Start
	include_once($phpbb_root_path . 'recruit/recruit_block.'.$phpEx);
	$recruit_config = recruit_config();
	
	if ($recruit_config['show_block'])
	{
		$template->assign_var('S_DISPLAY_RECRUIT', true);
	}
// Recruitment Block - End

/***** end blocks ********/

$title = 'Новости';
$template->assign_var('S_PORTAL', true);

page_header($title);

$template->set_filenames(array(
	'body' => 'dkp/news_body.html')
);

make_jumpbox(append_sid("{$phpbb_root_path}viewforum.$phpEx"));

// auto-refreshing portal is disabled
// uncomment this line if you want it back
//meta_refresh(60, append_sid("{$phpbb_root_path}portal.$phpEx")); 

page_footer();

?>
