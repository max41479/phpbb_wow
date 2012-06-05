<?
function gen_links()
{
	global $phpbb_root_path, $phpEx, $template, $user;
	$user->add_lang(array('mods/dkp_common'));
	$template->assign_vars(array(
		'L_PORTAL'		=> $user->lang['PORTAL'],
		'U_APPLY'		=> append_sid("{$phpbb_root_path}apply.$phpEx"),
		'U_PORTAL'		=> append_sid("{$phpbb_root_path}portal.$phpEx"),
		'U_DKP'			=> append_sid("{$phpbb_root_path}dkp.$phpEx", 'page=standings'),
		'L_DKPPAGE'		=> $user->lang['DKP'],
		'L_BBDKP'		=> $user->lang['FOOTERBBDKP'],
		'U_ABOUT'		=> append_sid("{$phpbb_root_path}aboutbbdkp.$phpEx")
	));
}

$phpbb_hook->register(array('template', 'display'), 'gen_links');
?>